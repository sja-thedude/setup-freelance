import dotenv from 'dotenv';
import express from 'express';
import path from 'path';

const app = express();

//=== init config ===
dotenv.config();

//=== Enable doc ===
app.use(express.static('doc'));

app.get('/', function(req, res) {
    res.sendFile(path.join(process.cwd(), './doc/index.html'));
});

export default app;
