const mysql = require('mysql2');

const conexao = mysql.createConnection({
  host: 'localhost',
  user: 'root',
  password: '',
  database: 'saasbot'
});

conexao.connect((err) => {
  if (err) throw err;
  console.log('✅ Conectado ao MySQL');
});

module.exports = conexao;
