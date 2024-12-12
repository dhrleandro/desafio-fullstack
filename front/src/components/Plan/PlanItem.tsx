import { Plan } from "@/api/interfaces"

export const PlanItem = ({ plan }: { plan: Plan }) => {
  return (
    <div className="cursor-pointer relative w-[354px] h-[326px] bg-secondaryBackground text-secondaryText rounded-lg shadow-lg before:absolute before:top-[28px] before:left-0 before:w-[80%] before:h-[72px] before:bg-accent z-0 active:bg-gray-900/20">
      <div className="relative z-[1] mt-[1.88rem] px-4 text-2xl font-bold text-accentText">
        <p>Até {plan.number_of_clients} vistorias</p>
        <p>/clientes ativos</p>
      </div>

      <div className="mt-[32px] px-4 text-2xl font-semibold text-secondaryText">
        <p className="text-2xl">Preço:</p>
        <p className="text-3xl font-bold">
          R$ {plan.price}
          <span className="text-xl">/mês</span>
        </p>
      </div>

      <div className="mt-[32px] px-4 text-2xl font-semibold text-secondaryText">
      <p className="text-2xl">Armazenamento:</p>
        <p className="text-3xl font-bold">
          R$ {plan.gigabytes_storage} GB
        </p>
      </div>
    </div>
  )
}