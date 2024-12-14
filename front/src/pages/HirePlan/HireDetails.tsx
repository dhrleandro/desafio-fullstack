import { PlanItem } from "@/components/Plan/PlanItem";
import { Payment, Plan } from "@/api/interfaces";
import { Button } from "@/components/Button";
import { useState } from "react";
import { PaymentRequest } from "./PaymentRequest";
import commands from "@/api/commands";

interface HireDetailsProps {
  activePlan?: Plan | null;
  newPlan: Plan;
  simulatedDate: Date;
  onSuccessCalculatePayment?: () => void;
}

export const HireDetails = ({ activePlan, newPlan, simulatedDate, onSuccessCalculatePayment }: HireDetailsProps) => {
  const [error, setError] = useState<boolean>(false);
  const [requesting, setRequesting] = useState<boolean>(false);
  const [payment, setPayment] = useState<Payment | null>(null);

  
  const calculatePayment = async () => {
    setError(false);
    setRequesting(true);

    const result = await commands.calculatePayment(newPlan.id, simulatedDate);
    setRequesting(false);

    if (!result) {
      setError(true);
    }

    onSuccessCalculatePayment?.();
    setPayment(result);
  }

  if (activePlan?.id === newPlan.id) {
    return (
      <div className="w-full flex flex-col gap-4 justify-center items-center">
        <h2 className="text-xl">O plano <span className="text-accent font-bold">{activePlan.description}</span> é o seu plano atual.</h2>
      </div>
    )
  }

  if (payment) {
    return (
      <PaymentRequest userHasActiveContract={!!activePlan} newPlan={newPlan} simulatedDate={simulatedDate} payment={payment}/>
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
      
      { <Button
        className="text-3xl mt-2"
        variant="primary"
        text={requesting ? "CONTRATANDO..." : "CONTRATAR PLANO"}
        onClick={requesting ? () => {} : calculatePayment}
      /> }

      {error && (
        <p className="text-red-500">Ocorreu um erro ao contratar o plano, tente novamente em alguns minutos.</p>
      )}
    </div>
  )
}