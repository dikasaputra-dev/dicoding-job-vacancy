import { apiClient } from "@/lib/api/api-client";

import type { VacancyDetail } from "../types/vacancy.types";

interface VacancyDetailResponse {
  data: VacancyDetail;
}

export async function getVacancy(
  vacancyId: number,
): Promise<VacancyDetail> {
  const response = await apiClient.get<VacancyDetailResponse>(
    `/vacancies/${vacancyId}`,
  );

  return response.data.data;
}
