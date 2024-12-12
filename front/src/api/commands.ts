import { HttpStatusCode } from "axios";
import { postData } from "./api";
import { Payment, PostContract } from "./interfaces";

const hirePlan = async (planId: number, simulatedDatetime?: string): Promise<boolean> => {
  const post = {
    plan_id: planId,
    simulated_datetime: simulatedDatetime
  } as PostContract;

  const result = await postData<PostContract, undefined>('/contracts', post);
  return (!result.error && result.status == HttpStatusCode.Created);
}

const switchPlan = async (planId: number, simulatedDatetime?: string): Promise<boolean> => {
  const post = {
    plan_id: planId,
    simulated_datetime: simulatedDatetime
  } as PostContract;

  const result = await postData<PostContract, undefined>('/contracts/switch-plan', post);
  return (!result.error && result.status == HttpStatusCode.Created);
}

const calculatePayment = async (planId: number, simulatedDatetime?: string): Promise<Payment | null> => {
  const post = {
    plan_id: planId,
    simulated_datetime: simulatedDatetime
  } as PostContract;

  const result = await postData<PostContract, Payment>('/contracts/calculate-payment', post);
  return (!result.error && result.status == HttpStatusCode.Ok)
    ? result.data
    : null;
}

const commands = { hirePlan, switchPlan, calculatePayment };

export default commands;