import { Container } from "@/components/Container";
import { useApiPlans } from "@/hooks/useApiPlans";
import { PlanItem } from "@/components/Plan/PlanItem";
import { Link } from "react-router-dom";

export const Home = () => {
  const { loading, plans } = useApiPlans();

  return (
    <Container title="Planos" loading={loading}>
      <div className="w-full flex justify-center items-center">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          {plans?.map((plan) => (
            <Link key={plan.id} to={`/hire-plan/${plan.id}`} className="flex justify-center">
              <PlanItem plan={plan}/>
            </Link>
          ))}
        </div>
      </div>
    </Container>
  )
}
