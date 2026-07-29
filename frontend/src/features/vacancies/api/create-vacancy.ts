import { apiClient } from "@/lib/api/api-client";

import type {
  CreateVacancyPayload,
  VacancyDetail,
} from "../types/vacancy.types";

interface CreateVacancyResponse {
  data: VacancyDetail;
  message: string;
}

export async function createVacancy(
  payload: CreateVacancyPayload,
): Promise<VacancyDetail> {
  const response = await apiClient.post<CreateVacancyResponse>(
    "/vacancies",
    payload,
  );

  return response.data.data;
}
