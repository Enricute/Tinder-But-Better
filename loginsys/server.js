const http = require('http');
const fs = require('fs');
const path = require('path');

const server = http.createServer((req, res) => {
  if (req.method === 'POST' && req.url === '/signup') {
    let body = '';
    req.on('data', chunk => {
      body += chunk.toString();
    });
    req.on('end', () => {
      const username = new URLSearchParams(body).get('uname');
      const password = new URLSearchParams(body).get('pwd');
      console.log(`Received signup request: username=${username}, password=${password}`);
      
      const usersFilePath = path.join(__dirname, 'users.json');
      if (!fs.existsSync(usersFilePath)) {
        console.log(`users.json file does not exist, creating...`);
        fs.writeFileSync(usersFilePath, '[]');
      }

      const users = JSON.parse(fs.readFileSync(usersFilePath));
      if (users.find(u => u.username === username)) {
        console.log(`Error: username '${username}' already exists`);
        const errorFilePath = path.join(__dirname, 'SignInError.log');
        if (!fs.existsSync(errorFilePath)) {
          console.log(`SignInError.log file does not exist, creating...`);
          fs.writeFileSync(errorFilePath, '');
        }
        fs.appendFileSync(errorFilePath, `${new Date().toISOString()} - Error: username '${username}' already exists\n`);
        res.statusCode = 409;
        res.end();
      } else {
        users.push({ username, password });
        fs.writeFileSync(usersFilePath, JSON.stringify(users));
        console.log(`User '${username}' signed up successfully`);
        res.statusCode = 200;
        res.end();
      }
    });
  } else {
    res.statusCode = 404;
    res.end();
  }
});

server.listen(3000, () => {
  console.log('Server is listening on port 3000');
});