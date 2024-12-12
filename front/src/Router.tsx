import { Route, Routes } from 'react-router-dom'
import { Layout } from './pages/Layout'
import { Home } from './pages/Home'
import { Profile } from './pages/Profile'
import { HirePlan } from './pages/HirePlan'


export function Router() {
  return (
    <Routes>
      <Route path="/" element={<Layout />}>
        <Route index element={<Home />}/>
        <Route path="/profile" element={<Profile />}/>
        <Route path="/hire-plan/:planId" element={<HirePlan />}/>
      </Route>
    </Routes>
  )
}
