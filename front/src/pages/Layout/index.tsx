import { Navbar } from "@/components/Navbar"
import { Outlet } from "react-router-dom"

export const Layout = () => {
  return (
    <div className="bg-primaryBackground min-h-screen w-full">
      <Navbar className="sticky top-0"/>
      <div className="p-2">
        <Outlet/>
      </div>
    </div>
  )
}