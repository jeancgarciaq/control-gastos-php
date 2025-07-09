# control-gastos-php
Aquí voy a desarrollar una pequeña aplicación de muestra de uso PDO  y MySQLi con patrón MVC en PHP

## Development Setup

### CSS Development with Tailwind

This project uses Tailwind CSS for styling. The CSS build process is managed through npm scripts.

#### Available Scripts

- **Build CSS (Production)**: Builds and minifies CSS for production
  ```bash
  npm run build:css
  ```

- **Watch CSS (Development)**: Watches for changes and rebuilds CSS automatically during development
  ```bash
  npm run watch:css
  ```

#### CSS File Structure

- **Input**: `src/input.css` - Contains Tailwind directives
- **Output**: `public/output.css` - Generated CSS file (included in .gitignore)
- **Config**: `tailwind.config.js` - Tailwind configuration with content paths

#### Development Workflow

1. Install dependencies:
   ```bash
   npm install
   ```

2. For development, start the CSS watcher:
   ```bash
   npm run watch:css
   ```

3. For production builds:
   ```bash
   npm run build:css
   ```

The CSS build process scans all PHP files in `app/Views/` for Tailwind classes and generates the optimized output file.
