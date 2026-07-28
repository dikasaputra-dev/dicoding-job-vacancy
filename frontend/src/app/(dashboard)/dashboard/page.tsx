"use client";

import Link from "next/link";

import { DashboardSidebar } from "@/components/layout/dashboard-sidebar";
import { Icon } from "@/components/ui/icon";
import { DashboardVacancyCard } from "@/features/vacancies/components/dashboard-vacancy-card";
import { useVacanciesQuery } from "@/features/vacancies/hooks/use-vacancies-query";
import type { VacancySummary } from "@/features/vacancies/types/vacancy.types";

export default function DashboardPage() {
  const { data, isPending, isError, error, refetch } = useVacanciesQuery({
    status: "all",
    perPage: 10,
  });

  function handleDelete(vacancy: VacancySummary) {
    window.alert(`Hapus lowongan: ${vacancy.title}`);
  }

  return (
    <main className="mx-auto flex w-full max-w-[1110px] flex-col md:min-h-[calc(100vh-70px)] md:flex-row">
      <DashboardSidebar />

      <section className="min-w-0 flex-1 px-5 py-8 sm:px-8 md:px-6">
        <div className="mb-8 flex flex-wrap items-center justify-between gap-4">
          <h1 className="text-2xl font-semibold tracking-[-0.02em] text-neutral-900">
            Lowongan Saya
          </h1>

          <Link
            href="/dashboard/vacancies/create"
            className="inline-flex h-10 items-center justify-center gap-2 rounded-sm bg-[#2d3e50] px-4 text-sm font-normal text-white transition-colors hover:bg-[#213142]"
          >
            <Icon name="plus" className="size-4" />
            Buat lowongan
          </Link>
        </div>

        {isPending ? <VacancyListSkeleton /> : null}

        {isError ? (
          <div
            role="alert"
            className="rounded-lg border border-red-200 bg-red-50 p-6 text-center"
          >
            <p className="text-sm text-red-700">
              {error instanceof Error
                ? error.message
                : "Lowongan gagal dimuat."}
            </p>

            <button
              type="button"
              onClick={() => refetch()}
              className="mt-4 rounded-sm bg-red-600 px-4 py-2 text-sm font-medium text-white"
            >
              Coba lagi
            </button>
          </div>
        ) : null}

        {data && data.data.length === 0 ? (
          <div className="rounded-lg border border-dashed border-neutral-300 p-10 text-center">
            <p className="font-medium text-neutral-800">Belum ada lowongan</p>
            <p className="mt-1 text-sm text-neutral-500">
              Buat lowongan pertama untuk mulai mencari kandidat.
            </p>
          </div>
        ) : null}

        {data && data.data.length > 0 ? (
          <div className="space-y-5">
            {data.data.map((vacancy) => (
              <DashboardVacancyCard
                key={vacancy.id}
                vacancy={vacancy}
                onDelete={handleDelete}
              />
            ))}
          </div>
        ) : null}
      </section>
    </main>
  );
}

function VacancyListSkeleton() {
  return (
    <div
      aria-label="Memuat daftar lowongan"
      aria-busy="true"
      className="space-y-5"
    >
      {[1, 2, 3].map((item) => (
        <div
          key={item}
          className="h-[138px] animate-pulse rounded-lg border border-neutral-200 bg-neutral-100"
        />
      ))}
    </div>
  );
}
