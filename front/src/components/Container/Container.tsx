import { ReactNode } from "react"
import { Spinner } from "../Spinner/Spinner";
import { useNavigate } from "react-router-dom";
import { IoMdArrowBack } from "react-icons/io";

interface ContainerProps {
  title: string;
  loading: boolean;
  backTo?: string;
  children: ReactNode;
}

export const Container = ({ title, loading, backTo, children }: ContainerProps) => {
  const navigate = useNavigate();

  return (
    <div className="flex flex-col w-full gap-4 text-primaryText pt-2 pb-[100px]">
      <div className="px-4 flex flex-row items-center justify-between w-full gap-4 border-b border-accent">
        <h1 className="w-full text-primaryText text-2xl font-bold">
          { backTo && <button onClick={() => navigate(backTo ?? '/')} className="w-[32px] h-[32px] mr-2 rounded-full hover:bg-accent hover:text-accentText inline-flex justify-center items-center">
            <IoMdArrowBack/>
          </button> }
          {title}
        </h1>
        {loading && <Spinner/>}
      </div>

      <div className="w-full flex flex-row items-center justify-center">
        <div className="w-full max-w-[2000px]">
          {children}
        </div>
      </div>
    </div>
  )
}