"use client";

import { useQuery } from "@tanstack/react-query";
import Link from "next/link";
import { useParams } from "next/navigation";

import { Icon } from "@/components/ui/icon";
import { getVacancy } from "@/features/vacancies/api/get-vacancy";
import { vacancyQueryKeys } from "@/features/vacancies/api/vacancy-query-keys";
import { VacancyLogo } from "@/features/vacancies/components/vacancy-logo";

export default function VacancyDetailPage() {
  const params = useParams<{ id: string }>();
  const vacancyId = Number(params.id);
  const isValidId = Number.isInteger(vacancyId) && vacancyId > 0;
  const { data, isPending, isError, refetch } = useQuery({
    queryKey: vacancyQueryKeys.detail(vacancyId),
    queryFn: () => getVacancy(vacancyId),
    enabled: isValidId,
  });

  if (!isValidId) {
    return <DetailMessage message="Lowongan tidak ditemukan." />;
  }

  if (isPending) {
    return <DetailSkeleton />;
  }

  if (isError) {
    return (
      <DetailMessage
        message="Detail lowongan gagal dimuat."
        onRetry={() => refetch()}
      />
    );
  }

  return (
    <main className="overflow-hidden rounded-t-2xl bg-white">
      <section className="border-b border-neutral-200">
        <div className="mx-auto flex w-full max-w-[1110px] items-center gap-4 px-5 py-8 sm:px-[60px]">
          <VacancyLogo
            src={data.company.logo_url}
            companyName={data.company.name}
            size="detail"
          />

          <div className="min-w-0">
            <h1 className="text-2xl font-semibold tracking-[-0.02em] text-neutral-900">
              {data.title}
            </h1>
            <p className="mt-2 text-sm text-neutral-500">
              Sektor Bisnis: {data.company.business_sector}
            </p>

            <div className="mt-2 flex flex-wrap gap-x-5 gap-y-2 text-sm text-neutral-700">
              <span className="inline-flex items-center gap-1.5">
                <Icon name="building" className="size-4" />
                {data.company.website_url ? (
                  <a
                    href={data.company.website_url}
                    target="_blank"
                    rel="noreferrer"
                    className="text-blue-600 underline underline-offset-2"
                  >
                    {data.company.name}
                  </a>
                ) : (
                  data.company.name
                )}
              </span>

              <span className="inline-flex items-center gap-1.5">
                <Icon name="location" className="size-4" />
                {data.is_remote ? "Remote" : data.location}
              </span>

              <span className="inline-flex items-center gap-1.5">
                <Icon name="users" className="size-4" />
                {data.company.employee_size} Karyawan
              </span>
            </div>
          </div>
        </div>
      </section>

      <section className="mx-auto w-full max-w-[730px] px-5 py-8 sm:px-0">
        <span className="inline-flex min-h-8 items-center rounded-full border border-blue-500 px-4 text-sm font-medium text-blue-600">
          {data.employment_type_label}
        </span>

        <div
          className="vacancy-description mt-7 text-base text-neutral-800"
          dangerouslySetInnerHTML={{ __html: data.description }}
        />

        <section className="mt-9" aria-labelledby="additional-information">
          <h2
            id="additional-information"
            className="text-xl leading-7 font-semibold tracking-[-0.017em] text-neutral-900"
          >
            Informasi Tambahan
          </h2>

          <div className="mt-2 grid min-h-[52px] grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
              <h3 className="font-semibold text-neutral-800">
                Pengalaman bekerja
              </h3>
              <p className="mt-2 text-neutral-700">
                {data.minimum_experience_label}
              </p>
            </div>

            <div>
              <h3 className="font-semibold text-neutral-800">
                Kandidat yang dibutuhkan
              </h3>
              <p className="mt-2 text-neutral-700">
                {data.candidate_count} kandidat
              </p>
            </div>
          </div>
        </section>

      </section>
    </main>
  );
}

interface DetailMessageProps {
  message: string;
  onRetry?: () => void;
}

function DetailMessage({ message, onRetry }: DetailMessageProps) {
  return (
    <main className="mx-auto flex min-h-[420px] max-w-[730px] items-center justify-center px-5 text-center">
      <div>
        <p className="font-medium text-neutral-800">{message}</p>
        {onRetry ? (
          <button
            type="button"
            onClick={onRetry}
            className="mt-4 rounded bg-[#2d3e50] px-4 py-2 text-sm font-medium text-white"
          >
            Coba lagi
          </button>
        ) : null}
        <Link href="/" className="mt-4 block text-sm text-blue-600">
          Kembali ke daftar lowongan
        </Link>
      </div>
    </main>
  );
}

function DetailSkeleton() {
  return (
    <main aria-busy="true" aria-label="Memuat detail lowongan">
      <div className="h-[158px] animate-pulse border-b border-neutral-200 bg-neutral-100" />
      <div className="mx-auto max-w-[730px] space-y-5 px-5 py-8 sm:px-0">
        <div className="h-8 w-24 animate-pulse rounded-full bg-neutral-100" />
        <div className="h-7 w-48 animate-pulse rounded bg-neutral-100" />
        <div className="h-32 animate-pulse rounded bg-neutral-100" />
        <div className="h-24 animate-pulse rounded bg-neutral-100" />
      </div>
    </main>
  );
}
