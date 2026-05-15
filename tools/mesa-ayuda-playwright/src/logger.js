import fs from 'node:fs';
import path from 'node:path';

export function createLogger({ logDir = 'logs', runId = new Date().toISOString().replace(/[:.]/g, '-') } = {}) {
  fs.mkdirSync(logDir, { recursive: true });
  const file = path.join(logDir, `mesa-ayuda-${runId}.log`);

  function write(level, message, context = {}) {
    const line = JSON.stringify({ ts: new Date().toISOString(), level, message, context });
    fs.appendFileSync(file, line + '\n', 'utf8');
    if (level === 'error') console.error(message, context);
    else console.log(message, context);
  }

  return {
    file,
    info: (message, context) => write('info', message, context),
    warn: (message, context) => write('warn', message, context),
    error: (message, context) => write('error', message, context),
  };
}
