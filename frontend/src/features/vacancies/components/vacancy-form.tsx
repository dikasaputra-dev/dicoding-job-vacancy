"use client";

import {
  useMutation,
  useQuery,
  useQueryClient,
} from "@tanstack/react-query";
import axios from "axios";
import Image from "next/image";
import Link from "next/link";
import { useRouter } from "next/navigation";
import {
  type FormEvent,
  type ReactNode,
  useRef,
  useState,
} from "react";

import { createVacancy } from "@/features/vacancies/api/create-vacancy";
import { getVacancy } from "@/features/vacancies/api/get-vacancy";
import { updateVacancy } from "@/features/vacancies/api/update-vacancy";
import { vacancyQueryKeys } from "@/features/vacancies/api/vacancy-query-keys";
import type {
  CreateVacancyPayload,
  EmploymentType,
  MinimumExperience,
} from "@/features/vacancies/types/vacancy.types";

const employmentTypes: Array<{
  label: string;
  value: EmploymentType;
}> = [
  { label: "Full-Time", value: "full-time" },
  { label: "Part-Time", value: "part-time" },
  { label: "Kontrak", value: "contract" },
  { label: "Intern", value: "internship" },
];

const experienceOptions: Array<{
  label: string;
  value: MinimumExperience;
}> = [
  { label: "Kurang dari 1 tahun", value: "less-than-1-year" },
  { label: "1-3 tahun", value: "1-3-years" },
  { label: "4-5 tahun", value: "4-5-years" },
  { label: "6-10 tahun", value: "6-10-years" },
  { label: "Lebih dari 10 tahun", value: "more-than-10-years" },
];

const descriptionTemplate = `
  <h2>Deskripsi Pekerjaan</h2>
  <p>Sebagai [Posisi Lowongan], Anda akan berpartisipasi dalam proses pembangunan aplikasi yang sedang dibangun dalam perusahaan [Nama Perusahaan]. Anda juga diharapkan mampu bekerja dalam tim.</p>
  <h2>Tanggung Jawab</h2>
  <ul>
    <li>Membuat atau memodifikasi program yang sudah ada.</li>
    <li>Bertanggung jawab dalam mengelola program.</li>
  </ul>
`;

interface ValidationResponse {
  message?: string;
  errors?: Record<string, string[]>;
}

interface VacancyFormProps {
  vacancyId?: number;
}

export function VacancyForm({ vacancyId }: VacancyFormProps) {
  const router = useRouter();
  const queryClient = useQueryClient();
  const editorRef = useRef<HTMLDivElement>(null);
  const [formError, setFormError] = useState<string | null>(null);
  const isEditing = vacancyId !== undefined;
  const detailQuery = useQuery({
    queryKey: vacancyQueryKeys.detail(vacancyId ?? 0),
    queryFn: () => getVacancy(vacancyId as number),
    enabled: isEditing,
  });
  const vacancy = detailQuery.data;
  const mutation = useMutation({
    mutationFn: (payload: CreateVacancyPayload) =>
      isEditing
        ? updateVacancy({
            vacancyId: vacancyId as number,
            payload,
          })
        : createVacancy(payload),
    onSuccess: async (savedVacancy) => {
      await queryClient.invalidateQueries({
        queryKey: vacancyQueryKeys.lists(),
      });
      queryClient.setQueryData(
        vacancyQueryKeys.detail(savedVacancy.id),
        savedVacancy,
      );
      router.push("/dashboard");
    },
    onError: (error) => {
      if (axios.isAxiosError<ValidationResponse>(error)) {
        const validationErrors = error.response?.data.errors;
        const firstError = validationErrors
          ? Object.values(validationErrors)[0]?.[0]
          : undefined;

        setFormError(
          firstError ??
            error.response?.data.message ??
            "Lowongan gagal dibuat.",
        );
        return;
      }

      setFormError("Lowongan gagal dibuat.");
    },
  });

  function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setFormError(null);

    const formData = new FormData(event.currentTarget);
    const salaryMaxValue = String(formData.get("salary_max") ?? "").trim();
    const description = editorRef.current?.innerHTML.trim() ?? "";

    if (!editorRef.current?.innerText.trim() || !description) {
      setFormError("Deskripsi wajib diisi.");
      editorRef.current?.focus();
      return;
    }

    const payload: CreateVacancyPayload = {
      title: String(formData.get("title") ?? "").trim(),
      position: String(formData.get("position") ?? ""),
      employment_type: String(
        formData.get("employment_type") ?? "",
      ) as EmploymentType,
      candidate_count: Number(formData.get("candidate_count")),
      expires_at: String(formData.get("expires_at") ?? ""),
      location: String(formData.get("location") ?? ""),
      is_remote: formData.get("is_remote") === "on",
      description,
      salary_min: Number(formData.get("salary_min")),
      salary_max: salaryMaxValue
        ? Number(salaryMaxValue)
        : undefined,
      show_salary: formData.get("show_salary") === "on",
      minimum_experience: String(
        formData.get("minimum_experience") ?? "",
      ) as MinimumExperience,
    };

    mutation.mutate(payload);
  }

  if (isEditing && detailQuery.isPending) {
    return <FormLoadingState />;
  }

  if (isEditing && detailQuery.isError) {
    return (
      <FormErrorState onRetry={() => detailQuery.refetch()} />
    );
  }

  const initialDescription = vacancy?.description ?? descriptionTemplate;

  function runEditorCommand(
    command: string,
    value?: string,
  ) {
    editorRef.current?.focus();
    document.execCommand(command, false, value);
  }

  function addLink() {
    const url = window.prompt("Masukkan URL tautan:");

    if (url) {
      runEditorCommand("createLink", url);
    }
  }

  function addImage() {
    const url = window.prompt("Masukkan URL gambar:");

    if (url) {
      runEditorCommand("insertImage", url);
    }
  }

  return (
    <main>
      <section className="border-b-2 border-blue-500 bg-[#18181b] text-white">
        <div className="mx-auto flex min-h-[176px] w-full max-w-[1110px] items-center px-5 py-8 sm:px-[60px]">
          <div>
            <h1 className="flex flex-wrap items-center gap-4 text-3xl font-semibold tracking-[-0.03em]">
              {isEditing
                ? "Edit lowongan pekerjaan"
                : "Buat lowongan pekerjaan"}
              <span className="relative h-11 w-[145px] overflow-hidden rounded-full">
                <Image
                  src="/assets/jobs-hero-professional.png"
                  alt=""
                  fill
                  priority
                  sizes="145px"
                  aria-hidden="true"
                  className="object-cover object-[65%_42%]"
                />
              </span>
            </h1>
            <p className="mt-4 max-w-md text-sm leading-5 text-neutral-200">
              Dicoding Jobs menghubungkan industri dengan talenta yang tepat.
              <br />
              Mencari tim baru tidak harus melelahkan dan boros biaya.
            </p>
          </div>
        </div>
      </section>

      <section className="mx-auto w-full max-w-[600px] px-5 py-10 sm:px-0">
        <form
          key={vacancy?.id ?? "create"}
          onSubmit={handleSubmit}
          className="space-y-7"
        >
          <FormField
            label="Judul lowongan"
            name="title"
            placeholder="Masukkan judul lowongan"
            hint="Contoh: Android Native Developer"
            defaultValue={vacancy?.title}
            required
          />

          <SelectField
            label="Posisi"
            name="position"
            placeholder="Pilih posisi yang dicari"
            options={[
              "Software Engineering",
              "Mobile Development",
              "Code Review",
              "Quality Assurance",
              "Product Management",
            ]}
            defaultValue={vacancy?.position}
            required
          />

          <RadioGroup
            legend="Tipe pekerjaan"
            name="employment_type"
            options={employmentTypes}
            defaultValue={vacancy?.employment_type}
          />

          <FormField
            label="Kandidat yang dibutuhkan"
            name="candidate_count"
            type="number"
            min={1}
            max={65535}
            placeholder="Masukkan jumlah kandidat yang dibutuhkan"
            defaultValue={vacancy?.candidate_count}
            required
          />

          <FormField
            label="Aktif hingga"
            name="expires_at"
            type="date"
            min={getLocalDate()}
            defaultValue={vacancy?.expires_at}
            required
          />

          <div>
            <SelectField
              label="Lokasi"
              name="location"
              placeholder="Pilih lokasi"
              options={[
                "Bandung",
                "Jakarta",
                "Yogyakarta",
                "Surabaya",
                "Bali",
              ]}
              defaultValue={vacancy?.location}
              required
            />
            <label className="mt-3 inline-flex items-center gap-2 text-sm text-neutral-700">
              <input
                type="checkbox"
                name="is_remote"
                defaultChecked={vacancy?.is_remote}
                className="size-4 accent-blue-600"
              />
              Bisa remote
            </label>
          </div>

          <div>
            <label
              id="description-label"
              htmlFor="description"
              className="mb-2 block text-sm font-medium text-neutral-800"
            >
              Deskripsi <RequiredMark />
            </label>
            <div className="border border-neutral-200">
              <div className="flex h-9 items-center gap-1 overflow-x-auto border-b border-neutral-200 px-4 text-neutral-700">
                <EditorButton
                  label="Jadikan heading"
                  onClick={() => runEditorCommand("formatBlock", "h2")}
                  className="text-base font-medium"
                >
                  B
                </EditorButton>
                <EditorButton
                  label="Italic"
                  onClick={() => runEditorCommand("italic")}
                  className="text-base italic"
                >
                  I
                </EditorButton>
                <EditorButton
                  label="Underline"
                  onClick={() => runEditorCommand("underline")}
                  className="text-base underline underline-offset-2"
                >
                  U
                </EditorButton>

                <span
                  aria-hidden="true"
                  className="mx-1 h-4 w-px shrink-0 bg-neutral-300"
                />

                <EditorButton label="Tambahkan tautan" onClick={addLink}>
                  <EditorToolbarIcon name="link" />
                </EditorButton>
                <EditorButton label="Tambahkan gambar" onClick={addImage}>
                  <EditorToolbarIcon name="image" />
                </EditorButton>
                <EditorButton
                  label="Format kode"
                  onClick={() => runEditorCommand("formatBlock", "pre")}
                >
                  <EditorToolbarIcon name="code" />
                </EditorButton>
              </div>
              <div
                ref={editorRef}
                id="description"
                role="textbox"
                aria-labelledby="description-label"
                aria-multiline="true"
                contentEditable
                suppressContentEditableWarning
                dangerouslySetInnerHTML={{
                  __html: initialDescription,
                }}
                className="vacancy-editor h-[300px] w-full overflow-y-scroll p-4 text-sm leading-6 outline-none"
              />
            </div>
            <p className="mt-2 text-xs text-neutral-500">
              Anda bisa mengubah template yang telah disediakan di atas.
            </p>
          </div>

          <div>
            <label className="mb-2 block text-sm font-medium text-neutral-800">
              Rentang gaji per bulan <RequiredMark />
            </label>
            <div className="flex items-center gap-4">
              <CurrencyField
                name="salary_min"
                placeholder="Minimum"
                defaultValue={vacancy?.salary_min}
                required
              />
              <span aria-hidden="true">-</span>
              <CurrencyField
                name="salary_max"
                placeholder="Maksimum (opsional)"
                defaultValue={vacancy?.salary_max ?? undefined}
              />
            </div>
            <p className="mt-2 text-xs text-neutral-500">
              Anda tidak perlu mengisi kolom “Maksimum” jika yang dimasukkan
              adalah gaji pokok.
            </p>
            <label className="mt-3 inline-flex items-center gap-2 text-sm text-neutral-700">
              <input
                type="checkbox"
                name="show_salary"
                defaultChecked={vacancy?.show_salary}
                className="size-4 accent-blue-600"
              />
              Tampilkan gaji
            </label>
            <p className="mt-1 text-xs text-neutral-500">
              Gaji akan ditampilkan di lowongan pekerjaan dan dapat dilihat
              oleh kandidat.
            </p>
          </div>

          <RadioGroup
            legend="Minimum pengalaman bekerja"
            name="minimum_experience"
            options={experienceOptions}
            defaultValue={vacancy?.minimum_experience}
          />

          {formError ? (
            <p role="alert" className="text-sm text-red-600">
              {formError}
            </p>
          ) : null}

          <div className="flex items-center gap-3">
            <button
              type="submit"
              disabled={mutation.isPending}
              className="inline-flex h-10 items-center justify-center rounded-sm bg-[#2d3e50] px-5 text-sm font-medium text-white disabled:cursor-not-allowed disabled:opacity-60"
            >
              {mutation.isPending
                ? isEditing
                  ? "Menyimpan..."
                  : "Membuat..."
                : isEditing
                  ? "Simpan perubahan"
                  : "Buat lowongan"}
            </button>
            <Link
              href="/dashboard"
              className="inline-flex h-10 items-center justify-center rounded-sm border border-neutral-200 px-5 text-sm text-neutral-700 hover:bg-neutral-50"
            >
              Batal
            </Link>
          </div>
        </form>
      </section>
    </main>
  );
}

function FormLoadingState() {
  return (
    <main aria-busy="true" aria-label="Memuat form lowongan">
      <div className="h-[176px] animate-pulse bg-neutral-900" />
      <div className="mx-auto max-w-[600px] space-y-7 px-5 py-10 sm:px-0">
        {[1, 2, 3, 4, 5].map((item) => (
          <div
            key={item}
            className="h-12 animate-pulse rounded bg-neutral-100"
          />
        ))}
      </div>
    </main>
  );
}

function FormErrorState({ onRetry }: { onRetry: () => void }) {
  return (
    <main className="mx-auto flex min-h-[480px] items-center justify-center px-5 text-center">
      <div>
        <p className="font-medium text-neutral-800">
          Data lowongan gagal dimuat.
        </p>
        <button
          type="button"
          onClick={onRetry}
          className="mt-4 rounded bg-[#2d3e50] px-4 py-2 text-sm text-white"
        >
          Coba lagi
        </button>
        <Link href="/dashboard" className="mt-4 block text-sm text-blue-600">
          Kembali ke dashboard
        </Link>
      </div>
    </main>
  );
}

interface FormFieldProps {
  label: string;
  name: string;
  type?: "text" | "number" | "date";
  placeholder?: string;
  hint?: string;
  required?: boolean;
  min?: number | string;
  max?: number;
  defaultValue?: string | number | null;
}

function FormField({
  label,
  name,
  type = "text",
  placeholder,
  hint,
  required,
  min,
  max,
  defaultValue,
}: FormFieldProps) {
  return (
    <div>
      <label
        htmlFor={name}
        className="mb-2 block text-sm font-medium text-neutral-800"
      >
        {label} {required ? <RequiredMark /> : null}
      </label>
      <input
        id={name}
        name={name}
        type={type}
        placeholder={placeholder}
        required={required}
        min={min}
        max={max}
        defaultValue={defaultValue ?? undefined}
        className={inputClassName}
      />
      {hint ? (
        <p className="mt-2 text-xs text-neutral-500">{hint}</p>
      ) : null}
    </div>
  );
}

interface SelectFieldProps {
  label: string;
  name: string;
  placeholder: string;
  options: string[];
  required?: boolean;
  defaultValue?: string;
}

function SelectField({
  label,
  name,
  placeholder,
  options,
  required,
  defaultValue,
}: SelectFieldProps) {
  return (
    <div>
      <label
        htmlFor={name}
        className="mb-2 block text-sm font-medium text-neutral-800"
      >
        {label} {required ? <RequiredMark /> : null}
      </label>
      <select
        id={name}
        name={name}
        required={required}
        defaultValue={defaultValue ?? ""}
        className={inputClassName}
      >
        <option value="" disabled>
          {placeholder}
        </option>
        {options.map((option) => (
          <option key={option} value={option}>
            {option}
          </option>
        ))}
      </select>
    </div>
  );
}

interface RadioGroupProps<TValue extends string> {
  legend: string;
  name: string;
  options: Array<{ label: string; value: TValue }>;
  defaultValue?: TValue;
}

function RadioGroup<TValue extends string>({
  legend,
  name,
  options,
  defaultValue,
}: RadioGroupProps<TValue>) {
  return (
    <fieldset>
      <legend className="mb-3 text-sm font-medium text-neutral-800">
        {legend} <RequiredMark />
      </legend>
      <div className="space-y-3">
        {options.map((option) => (
          <label
            key={option.value}
            className="flex items-center gap-3 text-sm text-neutral-700"
          >
            <input
              type="radio"
              name={name}
              value={option.value}
              required
              defaultChecked={option.value === defaultValue}
              className="size-4 accent-blue-600"
            />
            {option.label}
          </label>
        ))}
      </div>
    </fieldset>
  );
}

interface CurrencyFieldProps {
  name: string;
  placeholder: string;
  required?: boolean;
  defaultValue?: number;
}

function CurrencyField({
  name,
  placeholder,
  required,
  defaultValue,
}: CurrencyFieldProps) {
  return (
    <div className="flex min-w-0 flex-1">
      <span className="inline-flex h-12 items-center border border-r-0 border-neutral-200 bg-neutral-50 px-4 text-sm">
        Rp
      </span>
      <input
        type="number"
        name={name}
        aria-label={
          name === "salary_min" ? "Gaji minimum" : "Gaji maksimum"
        }
        min={0}
        required={required}
        defaultValue={defaultValue}
        placeholder={placeholder}
        className="h-12 min-w-0 flex-1 border border-neutral-200 px-3 text-sm outline-none focus:border-blue-500"
      />
    </div>
  );
}

function RequiredMark() {
  return (
    <span aria-hidden="true" className="text-red-500">
      *
    </span>
  );
}

interface EditorButtonProps {
  children: ReactNode;
  label: string;
  onClick: () => void;
  className?: string;
}

function EditorButton({
  children,
  label,
  onClick,
  className = "",
}: EditorButtonProps) {
  return (
    <button
      type="button"
      aria-label={label}
      title={label}
      onMouseDown={(event) => event.preventDefault()}
      onClick={onClick}
      className={`inline-flex size-8 shrink-0 items-center justify-center rounded hover:bg-neutral-100 ${className}`}
    >
      {children}
    </button>
  );
}

type EditorToolbarIconName = "code" | "image" | "link";

function EditorToolbarIcon({
  name,
}: {
  name: EditorToolbarIconName;
}) {
  if (name === "link") {
    return (
      <svg
        aria-hidden="true"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
        className="size-4"
      >
        <path d="m10 13.5 4-4" />
        <path d="m7.5 16.5-1 1a3.5 3.5 0 0 1-5-5l4-4a3.5 3.5 0 0 1 5 0" />
        <path d="m16.5 7.5 1-1a3.5 3.5 0 0 1 5 5l-4 4a3.5 3.5 0 0 1-5 0" />
      </svg>
    );
  }

  if (name === "image") {
    return (
      <svg
        aria-hidden="true"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
        className="size-4"
      >
        <rect x="3" y="4" width="18" height="16" rx="1" />
        <circle cx="9" cy="9" r="1.5" />
        <path d="m4 17 5-5 3.5 3.5 2-2L20 19" />
      </svg>
    );
  }

  return (
    <svg
      aria-hidden="true"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
      className="size-4"
    >
      <path d="m8 6-5 6 5 6M16 6l5 6-5 6M14 3l-4 18" />
    </svg>
  );
}

function getLocalDate(): string {
  const date = new Date();
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const day = String(date.getDate()).padStart(2, "0");

  return `${year}-${month}-${day}`;
}

const inputClassName =
  "h-12 w-full border border-neutral-200 bg-white px-3 text-sm text-neutral-800 outline-none placeholder:text-neutral-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-100";
