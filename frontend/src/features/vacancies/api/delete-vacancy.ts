import { apiClient } from "@/lib/api/api-client";

export async function deleteVacancy(vacancyId: number): Promise<void> {
  await apiClient.delete(`/vacancies/${vacancyId}`);
}
