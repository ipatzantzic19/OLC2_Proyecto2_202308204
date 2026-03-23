/**
 * Configuración Global de GolampiDE
 */

export const config = {
  // Configuración de la API
  api: {
    baseUrl: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
    timeout: 30000, // 30 segundos
    retries: 3,
  },

  // Configuración del editor
  editor: {
    fontSize: 14,
    fontFamily: "'Courier New', monospace",
    lineHeight: 1.5,
    tabSize: 2,
    theme: 'dark',
  },

  // Configuración del tema
  theme: {
    dark: {
      background: '#1e1e1e',
      foreground: '#d4d4d4',
      primary: '#4a9eff',
      secondary: '#2d2d2d',
      accent: '#6a9955',
    },
    light: {
      background: '#ffffff',
      foreground: '#333333',
      primary: '#0066cc',
      secondary: '#f0f0f0',
      accent: '#00aa00',
    },
  },

  // Configuración de características
  features: {
    enableAutoSave: true,
    autoSaveInterval: 30000, // 30 segundos
    enableHistoryLocalStorage: true,
    maxHistoryItems: 50,
    enableCodeHighlight: true,
    enableLineNumbers: true,
  },

  // Mensajes de la aplicación
  messages: {
    executionStart: 'Iniciando ejecución...',
    executionEnd: 'Ejecución completada',
    executionError: 'Error durante la ejecución',
    validationSuccess: 'Código válido',
    validationError: 'Error de validación',
    connectionError: 'Error de conexión con el servidor',
  },
};

export default config;
