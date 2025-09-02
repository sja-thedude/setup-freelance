import winston from 'winston';
import url from 'url';
import http from 'http';
import https from 'https';
import querystring from 'querystring';
import jwt from 'jsonwebtoken';

const jwtSecrectKey = process.env.JWT_SECRECT_KEY || '';
const apiUrl = process.env.APP_API || 'http://192.168.1.150:3333/api/v1/';

class Socket {
    constructor(io) {
        this.io = io;
        this.logger = winston.createLogger({
            transports: [
                new winston.transports.File({ filename: 'error.log', level: 'error' }),
                new winston.transports.File({ filename: 'combined.log' })
            ]
        });
    }

    /**
     * Check token from client
     */
    authenticate() {
        this.io.use((socket, next) => {
            return next();
            // const auth = socket.request.headers.authorization;

            // if(auth || (socket.handshake.query && socket.handshake.query.token)){
            //     const token = (auth) ? auth.replace('Bearer ', '') : socket.handshake.query.token;

            //     jwt.verify(token, jwtSecrectKey, (err, decoded) => {
            //         if(err) return next(err);

            //         socket.decoded = decoded;
            //         socket.token = token;

            //         return next();
            //     });
            // } else {
            //     return next(new Error('error_token_invalid'));
            // }
        });
    }

    /**
     * @api {websocket} ?token=jwt-token Connection
     * @apiHeader {String} Authorization Bearer your-access-token or ?token=your-access-token.
     * @apiName Connection
     * @apiGroup Socket
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "Bearer your-access-token"
     *     }
     * @apiParamExample {javascript} Data:
     *      const token = 'jwt-token';
     *      const socket = io.connect('protocol://host:port', {
     *          'query': 'token=' + token
     *      });
     * @apiVersion 1.0.0
     */
    connection() {
        this.io.on('connection', (socket) => {
            socket.on('notification', (data) => {
                 socket.broadcast.emit('handle_notification', data);
            });
        });
    }
}

export default Socket;
