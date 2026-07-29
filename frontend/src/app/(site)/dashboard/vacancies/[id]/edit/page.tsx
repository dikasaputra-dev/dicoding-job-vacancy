"use client";

import Link from "next/link";
import { useParams } from "next/navigation";
import { VacancyForm } from "@/features/vacancies/components/vacancy-form";

export default function EditVacancyPage() {
  const params = useParams<{ id: string }>();
  const vacancyId = Number(params.id);

  if (!Number.isInteger(vacancyId) || vacancyId <= 0) {
    return (
      <main className="mx-auto min-h-[480px] max-w-[730px] px-6 py-16">
        <h1 className="text-2xl font-semibold">Lowongan tidak valid</h1>
        <p className="mt-2 text-zinc-600">
          ID lowongan yang ingin diedit tidak valid.
        </p>
        <Link
          href="/dashboard"
          className="mt-6 inline-block text-blue-600 hover:underline"
        >
          Kembali ke dashboard
        </Link>
      </main>
    );
  }

  return <VacancyForm vacancyId={vacancyId} />;
}
