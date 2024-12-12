import { PlanItem } from "@/components/Plan/PlanItem";
import { Payment, Plan } from "@/api/interfaces";
import { Button } from "@/components/Button";
import { useEffect, useState } from "react";
import commands from "@/api/commands";
import { useNavigate } from "react-router-dom";

import qrcode from "@/assets/qrcode.png";

interface PaymentRequestProps {
  userHasActiveContract: boolean;
  newPlan: Plan;
  simulatedDate: string;
  payment: Payment;
}

export const PaymentRequest = ({  userHasActiveContract, newPlan, simulatedDate, payment}: PaymentRequestProps) => {
  const [error, setError] = useState<boolean>(false);
  const [success, setSuccess] = useState<boolean>(false);
  const [requesting, setRequesting] = useState<boolean>(false);

  const navigate = useNavigate();

  const hirePlan = async () => {
    setSuccess(false);
    setError(false);
    setRequesting(true);

    let result;
    if (userHasActiveContract) {
      result = await commands.switchPlan(newPlan.id, simulatedDate);
    } else {
      result = await commands.hirePlan(newPlan.id, simulatedDate);
    }
    setRequesting(false);

    setSuccess(result);
    setError(!result);
  }

  useEffect(() => {
    if (!success) return;
    const timout = setTimeout(() => {
      navigate("/");
    }, 2000);

    return () => clearTimeout(timout);
  }, [success]);

  const discontOrDifference = parseFloat(payment.discount) < 0
    ? 'Diferença: '
    : 'Desconto: ';

  return (
    <div className="w-full flex flex-col gap-4 justify-center items-center">
      <h1 className="text-2xl font-bold">Confirme o pagamento</h1>
      <div className="w-full flex flex-row gap-4 justify-center items-center">
        <img src={qrcode} className="w-[200px] h-[200px] rounded-xl shadow-md"/>
        <div className="flex flex-col gap-2">
          <p className="text-lg"><strong>Valor:</strong> R$ {payment.plan_price.replace(".", ",")}</p>
          <p className="text-lg"><strong>{discontOrDifference}</strong> R$ {payment.discount.replace(".", ",")}</p>
          <p className="text-lg"><strong>Total a pagar:</strong> R$ {payment.amount_charged.replace(".", ",")}</p>
        </div>
      </div>

      { !success && <Button
        className="text-xl mt-2"
        variant="primary"
        text={requesting ? "CONFIRMANDO..." : "CONFIRMAR PAGAMENTO"}
        onClick={requesting ? () => {} : hirePlan}
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