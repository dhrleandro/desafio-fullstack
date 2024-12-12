import { ReactNode } from "react"
import { Spinner } from "../Spinner/Spinner";

interface ContainerProps {
  title: string;
  loading: boolean;
  children: ReactNode;
}

export const Container = ({ title, loading, children }: ContainerProps) => {
  return (
    <div className="flex flex-col w-full gap-4 text-primaryText">
      <div className="px-4 flex flex-row items-center justify-between w-full gap-4 border-b border-accent">
        <h1 className="w-full text-primaryText text-2xl font-bold">
          {title}
        </h1>
        {loading && <Spinner/>}
      </div>
      {children}
    </div>
  )
}