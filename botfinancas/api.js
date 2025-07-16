const express = require('express');
const app = express();
const { processarMensagem } = require('./bot'); // Aqui você deve exportar sua função do bot

app.get('/api/bot', (req, res) => {
  const userMsg = req.query.message || '';
  const resposta = processarMensagem(userMsg); // função que processa a resposta
  res.send(resposta);
});

app.listen(3000, () => console.log('API do bot rodando na porta 3000'));
