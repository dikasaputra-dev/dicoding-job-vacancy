"use client";

import { useQuery } from "@tanstack/react-query";

import { getVacancies } from "../api/get-vacancies";
import { vacancyQueryKeys } from "../api/vacancy-query-keys";
import type { VacancyListParams } from "../types/vacancy.types";

export function useVacanciesQuery(params: VacancyListParams = {}) {
  return useQuery({
    queryKey: vacancyQueryKeys.list(params),
    queryFn: () => getVacancies(params),
  });
}
