import { Card } from "@/components/ui/card";

import type { VacancySummary } from "../types/vacancy.types";
import { formatVacancyDate } from "../lib/format-vacancy-date";
import { VacancyLogo } from "./vacancy-logo";

interface PublicVacancyCardProps {
  vacancy: VacancySummary;
}

export function PublicVacancyCard({ vacancy }: PublicVacancyCardProps) {
  const titleId = `public-vacancy-${vacancy.id}`;

  return (
    <Card className="flex flex-col gap-4 p-4 sm:min-h-[140px] sm:flex-row">
      <div className="shrink-0">
        <VacancyLogo
          src={vacancy.company.logo_url}
          companyName={vacancy.company.name}
        />
      </div>

      <div className="min-w-0 flex-1">
        <div className="flex flex-col gap-4 lg:flex-row lg:justify-between">
          <div>
            <h2 id={titleId} className="text-lg font-semibold text-neutral-900">
              {vacancy.position}
            </h2>

            <div className="mt-4 flex flex-wrap gap-x-5 gap-y-3 text-sm">
              <span>{vacancy.company.name}</span>

              <span>{vacancy.employment_type_label}</span>

              <span>{vacancy.location}</span>

              <span>{vacancy.minimum_experience_label}</span>
            </div>
          </div>

          <div className="shrink-0 text-sm leading-7 text-neutral-600 lg:text-right">
            <p>Dibuat pada {formatVacancyDate(vacancy.created_at)}</p>

            <p>Lamar sebelum {formatVacancyDate(vacancy.expires_at)}</p>
          </div>
        </div>
      </div>
    </Card>
  );
}
