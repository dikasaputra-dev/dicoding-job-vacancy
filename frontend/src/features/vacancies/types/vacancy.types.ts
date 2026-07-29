export type EmploymentType =
  | "full-time"
  | "part-time"
  | "contract"
  | "internship";

export type MinimumExperience =
  | "less-than-1-year"
  | "1-3-years"
  | "4-5-years"
  | "6-10-years"
  | "more-than-10-years";

export type VacancyStatusFilter = "active" | "expired" | "all";

export interface CompanySummary {
  id: number;
  name: string;
  logo_url: string | null;
}

export interface CompanyDetail extends CompanySummary {
  slug: string;
  business_sector: string;
  employee_size: string;
  headquarters_location: string;
  website_url: string | null;
}

export interface VacancySummary {
  id: number;
  title: string;
  position: string;
  employment_type: EmploymentType;
  employment_type_label: string;
  location: string;
  is_remote: boolean;
  minimum_experience: MinimumExperience;
  minimum_experience_label: string;
  expires_at: string;
  is_active: boolean;
  created_at: string | null;
  company: CompanySummary;
}

export interface PaginationLinks {
  first: string;
  last: string;
  prev: string | null;
  next: string | null;
}

export interface PaginationMeta {
  current_page: number;
  from: number | null;
  last_page: number;
  per_page: number;
  to: number | null;
  total: number;
}

export interface PaginatedResponse<TData> {
  data: TData[];
  links: PaginationLinks;
  meta: PaginationMeta;
}

export interface VacancyListParams {
  search?: string;
  status?: VacancyStatusFilter;
  page?: number;
  perPage?: number;
}

export interface VacancyDetail
  extends Omit<VacancySummary, "company"> {
  candidate_count: number;
  description: string;
  salary_min: number;
  salary_max: number | null;
  show_salary: boolean;
  updated_at: string | null;
  company: CompanyDetail;
}

export interface CreateVacancyPayload {
  title: string;
  position: string;
  employment_type: EmploymentType;
  candidate_count: number;
  expires_at: string;
  location: string;
  is_remote: boolean;
  description: string;
  salary_min: number;
  salary_max?: number;
  show_salary: boolean;
  minimum_experience: MinimumExperience;
}
