import { HttpStatusCode } from "axios";
import { postData } from "./api";
import { Payment, PostContract } from "./interfaces";
import datetime from "@/lib/datetime";

const hirePlan = async (planId: number, simulatedDatetime?: Date): Promise<boolean> => {
  const post = {
    plan_id: planId,
    simulated_datetime: datetime.UTCDateTimeToString(simulatedDatetime ?? new Date())
  } as PostContract;

  const result = await postData<PostContract, null>('/contracts', post);
  return (!result.error && result.status == HttpStatusCode.Created);
}

const switchPlan = async (planId: number, simulatedDatetime?: Date): Promise<boolean> => {
  const post = {
    plan_id: planId,
    simulated_datetime: datetime.UTCDateTimeToString(simulatedDatetime ?? new Date())
  } as PostContract;

  const result = await postData<PostContract, null>('/contracts/switch-plan', post);
  return (!result.error && result.status == HttpStatusCode.Created);
}

const calculatePayment = async (planId: number, simulatedDatetime?: Date): Promise<Payment | null> => {
  const post = {
    plan_id: planId,
    simulated_datetime: datetime.UTCDateTimeToString(simulatedDatetime ?? new Date())
  } as PostContract;

  const result = await postData<PostContract, Payment>('/contracts/calculate-payment', post);
  return (!result.error && result.status == HttpStatusCode.Ok)
    ? result.data
    : null;
}

const commands = { hirePlan, switchPlan, calculatePayment };

export default commands;