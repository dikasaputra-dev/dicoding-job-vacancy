import { apiClient } from "@/lib/api/api-client";

import type {
  CreateVacancyPayload,
  VacancyDetail,
} from "../types/vacancy.types";

interface UpdateVacancyResponse {
  data: VacancyDetail;
  message: string;
}

interface UpdateVacancyVariables {
  vacancyId: number;
  payload: CreateVacancyPayload;
}

export async function updateVacancy({
  vacancyId,
  payload,
}: UpdateVacancyVariables): Promise<VacancyDetail> {
  const response = await apiClient.put<UpdateVacancyResponse>(
    `/vacancies/${vacancyId}`,
    payload,
  );

  return response.data.data;
}
