import ReactDOM from 'react-dom/client'
import { StrictMode } from 'react'
import { ThemeProvider } from '@/providers/ThemeProvider.tsx'
import { App } from './App.tsx'

import './styles/global.css'

ReactDOM.createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <ThemeProvider>
      <App />
    </ThemeProvider>
  </StrictMode>,
)
