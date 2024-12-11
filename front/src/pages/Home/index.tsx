import { ThemeSwitch } from "@/components/ThemeSwitch"


export const Home = () => {
  return (
    <div className="bg-primaryBackground flex items-center justify-center h-screen">
      <ThemeSwitch />
      <h1 className="text-accent text-2xl">
        Desafio para Desenvolvedor - Inmediam
      </h1>
    </div>
  )
}