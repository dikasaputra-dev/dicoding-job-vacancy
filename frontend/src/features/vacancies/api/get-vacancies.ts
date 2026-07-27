import { apiClient } from "@/lib/api/api-client";

import type {
  PaginatedResponse,
  VacancyListParams,
  VacancySummary,
} from "../types/vacancy.types";

export async function getVacancies(
  params: VacancyListParams = {},
): Promise<PaginatedResponse<VacancySummary>> {
  const response = await apiClient.get<PaginatedResponse<VacancySummary>>(
    "/vacancies",
    {
      params: {
        search: params.search?.trim() || undefined,
        status: params.status ?? "active",
        page: params.page,
        per_page: params.perPage,
      },
    },
  );

  return response.data;
}
