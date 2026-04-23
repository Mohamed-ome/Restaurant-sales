import express from 'express';
import path from 'path';
import { fileURLToPath } from 'url';
import fs from 'fs';
import { createServer as createViteServer } from 'vite';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

async function startServer() {
  const app = express();
  const PORT = 3000;

  // Middleware to "process" .php files for the preview if requested directly
  // It just serves them as HTML, ignoring PHP tags
  app.use((req, res, next) => {
    if (req.path.endsWith('.php')) {
      const filePath = path.join(__dirname, 'php', req.path);
      if (fs.existsSync(filePath)) {
        let content = fs.readFileSync(filePath, 'utf8');
        
        // Basic mock of PHP includes
        content = content.replace(/<\?php include ['"](.+?)['"]; \?>/g, (match, p1) => {
          const includePath = path.join(__dirname, 'php', p1);
          return fs.existsSync(includePath) ? fs.readFileSync(includePath, 'utf8') : `<!-- Include not found: ${p1} -->`;
        });

        // Remove other PHP tags for cleaner preview
        content = content.replace(/<\?php[\s\S]*?\?>/g, '');
        
        res.setHeader('Content-Type', 'text/html');
        return res.send(content);
      }
    }
    next();
  });

  // Serve static files from php directory (assets etc)
  app.use(express.static(path.join(__dirname, 'php')));

  // Vite middleware for development
  if (process.env.NODE_ENV !== "production") {
    const vite = await createViteServer({
      server: { middlewareMode: true },
      appType: "spa",
    });
    app.use(vite.middlewares);
  } else {
    const distPath = path.join(process.cwd(), 'dist');
    app.use(express.static(distPath));
    app.get('*', (req, res) => {
      res.sendFile(path.join(distPath, 'index.html'));
    });
  }

  app.listen(PORT, "0.0.0.0", () => {
    console.log(`Server running on http://localhost:${PORT}`);
  });
}

startServer();
