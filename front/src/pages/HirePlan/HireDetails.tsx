import { PlanItem } from "@/components/Plan/PlanItem";
import { Plan } from "@/api/interfaces";
import { Button } from "@/components/Button";
import { useEffect, useState } from "react";
import commands from "@/api/commands";
import { useNavigate } from "react-router-dom";

export const HireDetails = ({ activePlan, newPlan }: {activePlan?: Plan | null, newPlan: Plan}) => {
  const [error, setError] = useState<boolean>(false);
  const [success, setSuccess] = useState<boolean>(false);
  const [requesting, setRequesting] = useState<boolean>(false);
  const navigate = useNavigate();

  const hirePlan = async () => {
    setSuccess(false);
    setError(false);
    setRequesting(true);

    const result = await commands.hirePlan(newPlan.id);
    setRequesting(false);

    setSuccess(result);
    setError(!result);
  }

  useEffect(() => {
    if (!success) return;
    const timout = setTimeout(() => {
      navigate("/");
    }, 1000);

    return () => clearTimeout(timout);
  }, [success]);

  if (activePlan?.id === newPlan.id) {
    return (
      <div className="w-full flex flex-col gap-4 justify-center items-center">
        <h2 className="text-xl">O plano <span className="text-accent font-bold">{activePlan.description}</span> é o seu plano atual.</h2>
      </div>
    )
  }

  return (
    <div className="w-full flex flex-col gap-4 justify-center items-center">
      {activePlan && (
        <>
          <h2 className="text-xl">ATENÇÃO: Você está trocando do plano <span className="text-accent font-bold">{activePlan.description}</span></h2>
          <PlanItem plan={activePlan} />
        </>
      )}
      <>
        { activePlan && <h2 className="text-xl">PARA: <span className="text-accent font-bold">{newPlan.description}</span></h2> }
        <PlanItem plan={newPlan} />
      </>
      
      { !success && <Button
        className="text-3xl mt-2"
        variant="primary"
        text={requesting ? "CONTRATANDO..." : "CONTRATAR PLANO"}
        onClick={hirePlan}
      /> }

      {success && (
        <p className="text-green-500">Plano contratado com sucesso!</p>
      )}

      {error && (
        <p className="text-red-500">Ocorreu um erro ao contratar o plano, tente novamente em alguns minutos.</p>
      )}
    </div>
  )
}