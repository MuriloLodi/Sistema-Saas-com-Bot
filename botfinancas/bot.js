// Módulos
const { Client } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const cron = require('node-cron');
const db = require('./db');

const client = new Client();

// Geração do QR Code
client.on('qr', qr => qrcode.generate(qr, { small: true }));

// Quando o bot estiver pronto
client.on('ready', () => {
  console.log('✅ Bot de finanças pronto!');

  // Envio automático do resumo diário às 20h
  cron.schedule('0 20 * * *', async () => {
    console.log('⏰ Executando resumo diário...');

    db.query('SELECT DISTINCT numero FROM usuarios', async (err, results) => {
      if (err) return console.error('Erro ao buscar usuários:', err.message);

      for (const usuario of results) {
        await enviarResumoDiario(usuario.numero);
      }
    });
  });
});

// Receber mensagens do usuário
client.on('message', async message => {
  if (!message.body) return;

  const texto = message.body.toLowerCase();
  const numero = message.from;

  const usuario_id = await buscarOuCriarUsuario(numero);

  if (texto.startsWith('ganhei')) {
    const { valor, descricao } = interpretarEntrada(texto);
    db.query('INSERT INTO ganhos (usuario_id, valor, descricao, data) VALUES (?, ?, ?, ?)',
      [usuario_id, valor, descricao, dataHoje()]);
    message.reply('💰 Ganho registrado!');
  }

  else if (texto.startsWith('gastei')) {
    const { valor, descricao } = interpretarEntrada(texto);
    db.query('INSERT INTO gastos (usuario_id, valor, descricao, data) VALUES (?, ?, ?, ?)',
      [usuario_id, valor, descricao, dataHoje()]);
    message.reply('💸 Gasto registrado!');
  }

  else if (texto.startsWith('investi')) {
    const { valor, descricao } = interpretarEntrada(texto);
    db.query('INSERT INTO investimentos (usuario_id, valor, descricao) VALUES (?, ?, ?)',
      [usuario_id, valor, descricao]);
    message.reply('📈 Investimento registrado!');
  }

  else if (texto.startsWith('resumo')) {
    const partes = texto.split(' ');
    const mes = partes[1] || dataHoje().slice(0, 7);
    await enviarResumo(message, usuario_id, mes);
  }

  else if (texto === 'saldo') {
    const mesAtual = dataHoje().slice(0, 7);
    const saldo = await calcularSaldo(usuario_id, mesAtual);
    message.reply(`💼 Saldo do mês: R$ ${saldo.toFixed(2)}`);
  }

  else if (texto.startsWith('ajuda')) {
    const ajuda = `📘 *Comandos disponíveis:*

💰 *Ganhei [valor] [descrição]*
💸 *Gastei [valor] [descrição]*
📈 *Investi [valor] [descrição]*
📊 *Resumo* - Resumo do mês atual
📅 *Resumo [ano-mês]* - Ex: resumo 2025-07
💼 *Saldo* - Mostra o saldo do mês
❓ *Ajuda* - Lista de comandos`;
    message.reply(ajuda);
  }
});

// Funções utilitárias
function interpretarEntrada(texto) {
  const partes = texto.split(' ');
  const valor = parseFloat(partes[1]) || 0;
  const descricao = partes.slice(2).join(' ') || 'sem descrição';
  return { valor, descricao };
}

function dataHoje() {
  return new Date().toISOString().split('T')[0];
}

function buscarOuCriarUsuario(numero) {
  return new Promise((resolve, reject) => {
    db.query('SELECT id FROM usuarios WHERE numero = ?', [numero], (err, results) => {
      if (err) return reject(err);
      if (results.length) return resolve(results[0].id);

      db.query('INSERT INTO usuarios (numero) VALUES (?)', [numero], (err, result) => {
        if (err) return reject(err);
        resolve(result.insertId);
      });
    });
  });
}

async function calcularSaldo(usuario_id, mes) {
  return new Promise((resolve) => {
    const mesLike = mes + '%';
    db.query(`
      SELECT
        (SELECT IFNULL(SUM(valor), 0) FROM ganhos WHERE usuario_id = ? AND data LIKE ?) as ganhos,
        (SELECT IFNULL(SUM(valor), 0) FROM gastos WHERE usuario_id = ? AND data LIKE ?) as gastos
    `, [usuario_id, mesLike, usuario_id, mesLike], (err, results) => {
      if (err) return resolve(0);
      const saldo = results[0].ganhos - results[0].gastos;
      resolve(saldo);
    });
  });
}

async function enviarResumo(message, usuario_id, mes) {
  const mesLike = mes + '%';
  db.query(`
    SELECT 'ganho' as tipo, valor, descricao FROM ganhos WHERE usuario_id = ? AND data LIKE ?
    UNION ALL
    SELECT 'gasto' as tipo, valor, descricao FROM gastos WHERE usuario_id = ? AND data LIKE ?
    UNION ALL
    SELECT 'investimento' as tipo, valor, descricao FROM investimentos WHERE usuario_id = ?
  `, [usuario_id, mesLike, usuario_id, mesLike, usuario_id], (err, results) => {
    if (err) return message.reply('❌ Erro ao gerar resumo.');

    const ganhos = results.filter(r => r.tipo === 'ganho');
    const gastos = results.filter(r => r.tipo === 'gasto');
    const investimentos = results.filter(r => r.tipo === 'investimento');

    const totalGanhos = ganhos.reduce((s, g) => s + g.valor, 0);
    const totalGastos = gastos.reduce((s, g) => s + g.valor, 0);
    const totalInvest = investimentos.reduce((s, i) => s + i.valor, 0);

    const formatar = lista => lista.map(i => `- R$ ${i.valor.toFixed(2)} - ${i.descricao}`).join('\n') || 'Nenhum.';

    const resumo = `📊 *Resumo Financeiro (${mes})*

💰 *Ganhos:*
${formatar(ganhos)}

💸 *Gastos:*
${formatar(gastos)}

📈 *Investimentos:*
${formatar(investimentos)}

📈 *Totais:*
+ R$ ${totalGanhos.toFixed(2)}
- R$ ${totalGastos.toFixed(2)}
= R$ ${(totalGanhos - totalGastos).toFixed(2)}`;

    message.reply(resumo);
  });
}

async function enviarResumoDiario(numero) {
  try {
    const usuario_id = await buscarOuCriarUsuario(numero);
    const saldo = await calcularSaldo(usuario_id, dataHoje().slice(0, 7));
    const resumo = `📊 *Resumo Diário*
Saldo acumulado: R$ ${saldo.toFixed(2)}
Lembre-se de registrar seus ganhos e gastos!`;
    await client.sendMessage(numero, resumo);
  } catch (err) {
    console.error(`❌ Erro ao enviar resumo para ${numero}:`, err.message);
  }
}

client.initialize();
