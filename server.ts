import express from 'express';
import path from 'path';
import { fileURLToPath } from 'url';
import fs from 'fs';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const PORT = 3000;

// Middleware to "process" .php files for the preview
// It just serves them as HTML, ignoring PHP tags
app.use((req, res, next) => {
  if (req.path.endsWith('.php') || req.path === '/') {
    const filePath = path.join(__dirname, 'php', req.path === '/' ? 'index.php' : req.path);
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

app.use(express.static(path.join(__dirname, 'php')));

app.listen(PORT, '0.0.0.0', () => {
  console.log(`PHP Preview Server running on http://localhost:${PORT}`);
});
