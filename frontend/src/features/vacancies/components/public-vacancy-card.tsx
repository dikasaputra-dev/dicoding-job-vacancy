import Link from "next/link";

import { Card } from "@/components/ui/card";
import { Icon } from "@/components/ui/icon";

import type { VacancySummary } from "../types/vacancy.types";
import { formatVacancyDate } from "../lib/format-vacancy-date";
import { VacancyLogo } from "./vacancy-logo";

interface PublicVacancyCardProps {
  vacancy: VacancySummary;
}

export function PublicVacancyCard({ vacancy }: PublicVacancyCardProps) {
  const titleId = `public-vacancy-${vacancy.id}`;

  return (
    <Link
      href={`/vacancies/${vacancy.id}`}
      aria-labelledby={titleId}
      className="block rounded-lg outline-none transition focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
    >
      <Card className="flex flex-col gap-4 p-4 transition-colors hover:border-blue-300 hover:bg-blue-50/20 sm:min-h-[140px] sm:flex-row">
        <div className="shrink-0">
          <VacancyLogo
            src={vacancy.company.logo_url}
            companyName={vacancy.company.name}
          />
        </div>

        <div className="min-w-0 flex-1">
          <div className="flex flex-col gap-4 lg:flex-row lg:justify-between">
            <div>
              <h2
                id={titleId}
                className="text-lg font-semibold text-neutral-900"
              >
                {vacancy.title}
              </h2>

              <div className="mt-4 grid gap-x-5 gap-y-2 text-sm text-neutral-700 sm:grid-cols-2">
                <span className="inline-flex items-center gap-1.5">
                  <Icon name="building" className="size-4" />
                  {vacancy.company.name}
                </span>

                <span>{vacancy.employment_type_label}</span>

                <span className="inline-flex items-center gap-1.5">
                  <Icon name="location" className="size-4" />
                  {vacancy.is_remote ? "Remote" : vacancy.location}
                </span>

                <span className="inline-flex items-center gap-1.5">
                  <Icon name="briefcase" className="size-4" />
                  {vacancy.minimum_experience_label}
                </span>
              </div>
            </div>

            <div className="shrink-0 text-sm leading-7 text-neutral-500 lg:text-right">
              <p>Dibuat pada {formatVacancyDate(vacancy.created_at)}</p>

              <p>Lamar sebelum {formatVacancyDate(vacancy.expires_at)}</p>
            </div>
          </div>
        </div>
      </Card>
    </Link>
  );
}
