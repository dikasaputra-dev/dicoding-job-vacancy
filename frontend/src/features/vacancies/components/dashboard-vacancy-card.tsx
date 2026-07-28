"use client";

import Link from "next/link";

import { Card } from "@/components/ui/card";
import { Icon } from "@/components/ui/icon";

import type { VacancySummary } from "../types/vacancy.types";
import { formatVacancyDate } from "../lib/format-vacancy-date";
import { VacancyLogo } from "./vacancy-logo";

interface DashboardVacancyCardProps {
  vacancy: VacancySummary;
  onDelete: (vacancy: VacancySummary) => void;
}

export function DashboardVacancyCard({
  vacancy,
  onDelete,
}: DashboardVacancyCardProps) {
  return (
    <Card className="flex min-h-[138px] flex-col gap-4 p-4 sm:flex-row">
      <div className="shrink-0">
        <VacancyLogo
          src={vacancy.company.logo_url}
          companyName={vacancy.company.name}
          size="compact"
        />
      </div>

      <div className="min-w-0 flex-1">
        <h2 className="text-base font-semibold text-neutral-700">
          {vacancy.title}
        </h2>

        <div className="mt-2 flex flex-wrap gap-x-5 gap-y-2 text-sm leading-5 text-neutral-500">
          <span className="inline-flex items-center gap-1.5">
            <Icon name="upload" className="size-4" />
            Dibuat: {formatVacancyDate(vacancy.created_at)}
          </span>

          <span className="inline-flex items-center gap-1.5">
            <Icon name="clock" className="size-4" />
            Aktif hingga: {formatVacancyDate(vacancy.expires_at)}
          </span>
        </div>

        <div className="mt-4 flex flex-wrap gap-3">
          <Link
            href={`/dashboard/vacancies/${vacancy.id}/edit`}
            className="
              inline-flex
              h-9
              items-center
              justify-center
              gap-2
              rounded-sm
              border
              border-neutral-200
              px-3
              text-sm
              text-neutral-700
              hover:bg-neutral-50
            "
          >
            <Icon name="edit" className="size-4" />
            Edit
          </Link>

          <button
            type="button"
            onClick={() => onDelete(vacancy)}
            className="
              inline-flex
              h-9
              items-center
              justify-center
              gap-2
              rounded-sm
              border
              border-red-100
              bg-red-100
              px-3
              text-sm
              text-red-600
              hover:bg-red-200
            "
          >
            <Icon name="trash" className="size-4" />
            Hapus
          </button>
        </div>
      </div>
    </Card>
  );
}
