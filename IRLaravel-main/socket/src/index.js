import http from 'http';
import https from 'https';
import fs from 'fs';
import path from 'path';
import winston from 'winston';
import socketIo from 'socket.io';
import {app, socket} from './app';

const port = process.env.DEV_PORT || 80;
const portHttps = process.env.DEV_PORT_HTTPS || 443;
const options = {
    key: fs.readFileSync(path.join(process.cwd(), './build/fixtures/keys/privkey.pem')),
    cert: fs.readFileSync(path.join(process.cwd(), './build/fixtures/keys/fullchain.pem'))
};

class Server {
    constructor() {
        this.logger = winston.createLogger({
            transports: [
                new winston.transports.File({ filename: 'error.log', level: 'error' }),
                new winston.transports.File({ filename: 'combined.log' })
            ]
        });
    }

    run() {
        this.http();
        this.https();
    }

    http() {
		//=== HTTP Server ===
		const httpServer = http.createServer(app);
		const io = socketIo(httpServer);
		const socketClass = new socket(io);

		// set up initialization and authorization method
		socketClass.authenticate();
		socketClass.connection();

		// listen server with port
		httpServer.listen(port, () => {
		this.logger.info('Express server listening on http://localhost:' + httpServer.address().port);
		});

		// check error
		httpServer.on('error', this.onError);

		// log server andress
		httpServer.on('listening', () => {
		this.logger.info('Listening on port: ' + httpServer.address().port);
		});
    }

    https() {
		//=== HTTP Server ===
		const httpsServer = https.createServer(options, app);
		const io = socketIo(httpsServer);
		const socketClass = new socket(io);

		// set up initialization and authorization method
		socketClass.authenticate();
		socketClass.connection();

		// listen server with port
		httpsServer.listen(portHttps, () => {
		this.logger.info('Express server listening on http://localhost:' + httpsServer.address().port);
		});

		// check error
		httpsServer.on('error', this.onError);

		// log server andress
		httpsServer.on('listening', () => {
		this.logger.info('Listening on port: ' + httpsServer.address().port);
		});
    }

    onError(error) {
        if (typeof error.syscall != 'undefined' && error.syscall !== 'listen') {
            throw error;
        }

        const bind = typeof port === 'string' ? 'Pipe ' + port : 'Port ' + port;

        //=== handle specific listen errors with friendly messages ===
        if (typeof error.code != 'undefined') {
            switch (error.code) {
                case 'EACCES':
                    this.logger.error(bind + ' requires elevated privileges');
                    process.exit(1);

                    break;
                case 'EADDRINUSE':
                    this.logger.error(bind + ' is already in use');
                    process.exit(1);

                    break;
                default:
                    throw error;
            }
        }
    }
}

new Server().run();
