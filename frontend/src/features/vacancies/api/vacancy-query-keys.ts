import type { VacancyListParams } from "../types/vacancy.types";

export const vacancyQueryKeys = {
  all: ["vacancies"] as const,

  lists: () => [...vacancyQueryKeys.all, "list"] as const,

  list: (params: VacancyListParams) =>
    [...vacancyQueryKeys.lists(), params] as const,

  details: () => [...vacancyQueryKeys.all, "detail"] as const,

  detail: (vacancyId: number) =>
    [...vacancyQueryKeys.details(), vacancyId] as const,
};
