import axios from 'axios'
import { RequestResult, User } from './interfaces';

type PromiseResult<T> = Promise<RequestResult<T>>;

const result = <T>(error: boolean, response: boolean, data: T | null = null, status: number = 0): RequestResult<T> => {
  return {
    error,
    response,
    status,
    data
  } as RequestResult<T>;
}

const url = import.meta.env.VITE_API_URL
const api = axios.create({
  baseURL: url,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

api.interceptors.response.use(
  (response) => response,
  (error) => Promise.reject(error)
);


export const fetchData = async <T>(uri: string): PromiseResult<T> => {
  try {
    const response = await axios.get(`${url}${uri}`);
    return result(false, true, response.data, response.status);

  } catch (error: any) {
    if (error.response) {
      return result(true, true, error.response.data, error.response.status);
    }

    if (error.request) {
      return result(true, false, error.request);
    }

    return result(true, false, error.message);
  }
};
