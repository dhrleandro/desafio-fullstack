import { HttpStatusCode } from "axios";
import { postData } from "./api";
import { Payment, PostContract } from "./interfaces";

const numberToStringTwoDigits = (num: number): string => {
  if (num < 10) return `0${num}`;
  return num.toString();
}

const utcDateTime = (date?: Date): string|undefined => {
  if (!date) return;
  const month = numberToStringTwoDigits(date.getUTCMonth() + 1);
  const day = numberToStringTwoDigits(date.getUTCDate());

  return `${date.getUTCFullYear()}-${month}-${day} ${date.getUTCHours()}:${date.getUTCMinutes()}:${date.getUTCSeconds()}`;
}

const hirePlan = async (planId: number, simulatedDatetime?: Date): Promise<boolean> => {
  const post = {
    plan_id: planId,
    simulated_datetime: simulatedDatetime?.toISOString()
  } as PostContract;

  const result = await postData<PostContract, null>('/contracts', post);
  return (!result.error && result.status == HttpStatusCode.Created);
}

const switchPlan = async (planId: number, simulatedDatetime?: Date): Promise<boolean> => {
  const post = {
    plan_id: planId,
    simulated_datetime: simulatedDatetime?.toISOString()
  } as PostContract;

  const result = await postData<PostContract, null>('/contracts/switch-plan', post);
  return (!result.error && result.status == HttpStatusCode.Created);
}

const calculatePayment = async (planId: number, simulatedDatetime?: Date): Promise<Payment | null> => {
  const post = {
    plan_id: planId,
    simulated_datetime: utcDateTime(simulatedDatetime)
  } as PostContract;

  const result = await postData<PostContract, Payment>('/contracts/calculate-payment', post);
  return (!result.error && result.status == HttpStatusCode.Ok)
    ? result.data
    : null;
}

const commands = { hirePlan, switchPlan, calculatePayment };

export default commands;