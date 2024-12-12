import { Route, Routes } from 'react-router-dom'
import { Layout } from './pages/Layout'
import { Home } from './pages/Home'
import { Profile } from './pages/Profile'


export function Router() {
  return (
    <Routes>
      <Route path="/" element={<Layout />}>
        <Route index element={<Home />}/>
        <Route path="/profile" element={<Profile />}/>
      </Route>
    </Routes>
  )
}
