const express = require('express');
const app = express();
const mysql = require('mysql');

const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: 'password',
    database: 'database_name'
});

db.connect((err) => {
    if (err) {
        console.error(err);
        return;
    }
    console.log('Connected to database');
});

app.post('/login', (req, res) => {
    const { username, password } = req.body;
    const query = 'SELECT * FROM owner WHERE username = ? AND password = ?';

    db.query(query, [username, password], (err, results) => {
        if (err) {
            console.error(err);
            res.status(500).json({ message: 'Error occurred while querying database' });
            return;
        }

        if (results.length === 0) {
            res.status(401).json({ message: 'Invalid username or password' });
            return;
        }

        const user = results[0];
        const token = generateToken(user);

        res.json({ token, user });
    });
});

app.listen(3000, () => {
    console.log('Server listening on port 3000');
});
