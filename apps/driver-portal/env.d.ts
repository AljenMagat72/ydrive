declare global {
  namespace NodeJS {
    interface ProcessEnv {
      YDRIVE_API: string;
      CHART_KEY: string;
      NODE_ENV: 'development' | 'production' | 'test';
    }
  }
}

export { }