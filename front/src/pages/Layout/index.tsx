import { Navbar } from "@/components/Navbar"
import { Outlet } from "react-router-dom"

export const Layout = () => {
  return (
    <div className="relative bg-primaryBackground min-h-screen w-full">
      <Navbar className="sticky top-0 z-50"/>
      <div className="p-2">
        <Outlet/>
      </div>
    </div>
  )
}