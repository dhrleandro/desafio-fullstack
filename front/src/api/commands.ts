import { HttpStatusCode } from "axios";
import { postData } from "./api";
import { PostContract } from "./interfaces";

const hirePlan = async (planId: number): Promise<boolean> => {
  const post = {
    plan_id: planId
  } as PostContract;

  const result = await postData<PostContract, undefined>('/contracts', post);
  return (!result.error && result.status == HttpStatusCode.Created);
}

// export const switchPlan = (activePlan: Plan, newPlan: Plan) => {}

const commands = { hirePlan };

export default commands;