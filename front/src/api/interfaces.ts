export interface RequestResult<T> {
  error: boolean,
  response: boolean,
  status: number,
  data: T | null
}

export interface User {
  name: string;
  email: string;
}
