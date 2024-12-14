import { Container } from "@/components/Container";
import { useApiPlans } from "@/hooks/useApiPlans";
import { useParams } from "react-router-dom";
import { Plan } from "@/api/interfaces";
import { useEffect, useState } from "react";
import { Spinner } from "@/components/Spinner/Spinner";
import { HireDetails } from "./HireDetails";
import { useApiUser } from "@/hooks/useApiUser";
import { DateSelector } from "./DateSelector";

export const HirePlan = () => {
  const { planId } = useParams();

  const { loading, plans } = useApiPlans();
  const { user } = useApiUser();

  const [plan, setPlan] = useState<Plan | null>(null);
  const [simulatedDate, setSimulatedDate] = useState<Date>(new Date());
  const [showDateSelector, setShowDateSelector] = useState<boolean>(true);


  const getActivePlan = (): Plan | null => {
    if (!user || !user.active_contract || !plans) return null;

    const activePlan = plans.find((plan) => plan.id === user?.active_contract?.plan_id);
    return activePlan ? activePlan : null;
  }

  useEffect(() => {
    if (!plans || !planId) return;
    const plan = plans.find((plan) => plan.id === parseInt(planId));
    if (!plan) return;
    setPlan(plan);
  }, [plans]);

  const onDateSelected = (date: Date) => {
    setSimulatedDate(date);
  }

  return (
    <Container title={`Contrar Plano: ${plan?.description}`} loading={loading} backTo="/">
      {showDateSelector && <DateSelector onChange={onDateSelected}/>}
      <div className="w-full flex justify-center items-center">
        {!plan && <Spinner />}
        {plan && <HireDetails
          activePlan={getActivePlan()}
          newPlan={plan}
          simulatedDate={simulatedDate}
          onSuccessCalculatePayment={() => setShowDateSelector(false)}
        />}
      </div>
    </Container>
  )
}
