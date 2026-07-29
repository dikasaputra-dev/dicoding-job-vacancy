"use client";

import Image from "next/image";
import { useDeferredValue, useState } from "react";

import { Icon } from "@/components/ui/icon";
import { PublicVacancyCard } from "@/features/vacancies/components/public-vacancy-card";
import { useVacanciesQuery } from "@/features/vacancies/hooks/use-vacancies-query";

export default function HomePage() {
  const [search, setSearch] = useState("");
  const deferredSearch = useDeferredValue(search.trim());
  const { data, isPending, isError, error, refetch } = useVacanciesQuery({
    search: deferredSearch || undefined,
    status: "active",
    perPage: 20,
  });

  return (
    <main>
      <section className="bg-[#18181b] text-white">
        <div className="mx-auto flex min-h-[164px] w-full max-w-[1110px] items-center px-5 py-8 sm:px-[60px]">
          <div>
            <p className="mb-4 text-sm font-semibold text-blue-500">
              Dicoding Jobs
            </p>

            <h1 className="text-3xl leading-[1.25] font-semibold tracking-[-0.03em] sm:text-[32px]">
              Temukan lowongan yang
              <span className="mt-1 flex flex-wrap items-center gap-4">
                cocok untuk kamu
                <span className="relative inline-block h-10 w-[126px] overflow-hidden rounded-full">
                  <Image
                    src="/assets/jobs-hero-professional.png"
                    alt=""
                    fill
                    priority
                    sizes="126px"
                    aria-hidden="true"
                    className="object-cover object-[65%_42%]"
                  />
                </span>
              </span>
            </h1>
          </div>
        </div>
      </section>

      <section className="mx-auto w-full max-w-[960px] px-5 py-10 sm:px-8">
        <div className="mb-5 flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
          <h2 className="text-2xl font-semibold tracking-[-0.02em] text-neutral-900">
            Daftar Pekerjaan Terbaru
          </h2>

          <label className="relative block w-full md:max-w-[360px]">
            <span className="sr-only">Cari pekerjaan berdasarkan judul</span>
            <Icon
              name="search"
              className="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-neutral-500"
            />
            <input
              type="search"
              value={search}
              onChange={(event) => setSearch(event.target.value)}
              placeholder="Pekerjaan apa yang sedang kamu cari?"
              className="h-12 w-full rounded border border-neutral-200 bg-white pr-4 pl-12 text-sm text-neutral-800 outline-none transition placeholder:text-neutral-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            />
          </label>
        </div>

        {isPending ? <VacancyListSkeleton /> : null}

        {isError ? (
          <div
            role="alert"
            className="rounded-lg border border-red-200 bg-red-50 p-8 text-center"
          >
            <p className="text-sm text-red-700">
              {error instanceof Error
                ? error.message
                : "Daftar lowongan gagal dimuat."}
            </p>
            <button
              type="button"
              onClick={() => refetch()}
              className="mt-4 rounded bg-red-600 px-4 py-2 text-sm font-medium text-white"
            >
              Coba lagi
            </button>
          </div>
        ) : null}

        {data && data.data.length === 0 ? (
          <div className="rounded-lg border border-dashed border-neutral-300 p-10 text-center">
            <p className="font-medium text-neutral-800">
              Lowongan tidak ditemukan
            </p>
            <p className="mt-1 text-sm text-neutral-500">
              Coba gunakan judul pekerjaan yang berbeda.
            </p>
          </div>
        ) : null}

        {data && data.data.length > 0 ? (
          <div className="space-y-5">
            {data.data.map((vacancy) => (
              <PublicVacancyCard key={vacancy.id} vacancy={vacancy} />
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
      aria-label="Memuat daftar pekerjaan"
      aria-busy="true"
      className="space-y-5"
    >
      {[1, 2, 3, 4].map((item) => (
        <div
          key={item}
          className="h-[140px] animate-pulse rounded-lg border border-neutral-200 bg-neutral-100"
        />
      ))}
    </div>
  );
}
