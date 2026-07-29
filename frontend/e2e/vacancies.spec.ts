import { expect, test, type Page } from "@playwright/test";

const companyDetail = {
  id: 1,
  name: "Dicoding Indonesia",
  logo_url: null,
  slug: "dicoding-indonesia",
  business_sector: "Technology",
  employee_size: "50-100 Karyawan",
  headquarters_location: "Bandung",
  website_url: "https://www.dicoding.com",
};

type VacancyRecord = ReturnType<typeof vacancyFromPayload>;

const initialVacancies: VacancyRecord[] = [
  createVacancyRecord({
    id: 1,
    title: "Product Engineer",
    position: "Software Engineering",
    minimumExperience: "1-3-years",
    minimumExperienceLabel: "1-3 tahun",
  }),
  createVacancyRecord({
    id: 2,
    title: "Android Developer",
    position: "Mobile Development",
    minimumExperience: "4-5-years",
    minimumExperienceLabel: "4-5 tahun",
    isRemote: true,
  }),
];

test.beforeEach(async ({ page }) => {
  await mockVacancyApi(page);
});

test("job seeker can open the vacancies list page", async ({ page }) => {
  await page.goto("/");

  await expect(
    page.getByRole("heading", { name: "Daftar Pekerjaan Terbaru" }),
  ).toBeVisible();
  await expect(
    page.getByRole("heading", { name: "Product Engineer" }),
  ).toBeVisible();
  await expect(
    page.getByRole("heading", { name: "Android Developer" }),
  ).toBeVisible();
});

test("job seeker can search a vacancy by title", async ({ page }) => {
  await page.goto("/");

  await page
    .getByRole("searchbox", { name: "Cari pekerjaan berdasarkan judul" })
    .fill("Android");

  await expect(
    page.getByRole("heading", { name: "Android Developer" }),
  ).toBeVisible();
  await expect(
    page.getByRole("heading", { name: "Product Engineer" }),
  ).toBeHidden();
});

test("job seeker sees an empty state when no vacancy matches", async ({
  page,
}) => {
  await page.goto("/");

  await page
    .getByRole("searchbox", { name: "Cari pekerjaan berdasarkan judul" })
    .fill("Data Scientist");

  await expect(page.getByText("Lowongan tidak ditemukan")).toBeVisible();
  await expect(
    page.getByText("Coba gunakan judul pekerjaan yang berbeda."),
  ).toBeVisible();
});

test("job seeker can open a vacancy detail from its card", async ({ page }) => {
  await page.goto("/");

  await page.getByRole("link", { name: "Product Engineer" }).click();

  await expect(page).toHaveURL(/\/vacancies\/1$/);
  await expect(
    page.getByRole("heading", { name: "Product Engineer", level: 1 }),
  ).toBeVisible();
  await expect(
    page.getByRole("heading", { name: "Job Description" }),
  ).toBeVisible();
  await expect(page.getByText("Build impactful products")).toBeVisible();
  await expect(
    page.getByRole("heading", { name: "Informasi Tambahan" }),
  ).toBeVisible();
});

test("recruiter can create a vacancy and see it on the dashboard", async ({
  page,
}) => {
  await page.goto("/dashboard");
  await page.getByRole("link", { name: "Buat lowongan" }).click();

  await fillVacancyForm(page, "Frontend Engineer");
  await page.getByRole("button", { name: "Buat lowongan" }).click();

  await expect(page).toHaveURL(/\/dashboard$/);
  await expect(
    page.getByRole("article", { name: "Frontend Engineer" }),
  ).toBeVisible();
});

test("recruiter can edit a vacancy", async ({ page }) => {
  await page.goto("/dashboard");

  const productCard = page.getByRole("article", {
    name: "Product Engineer",
  });
  await productCard.getByRole("link", { name: "Edit" }).click();

  await expect(
    page.getByRole("heading", { name: "Edit lowongan pekerjaan" }),
  ).toBeVisible();
  await page.getByLabel("Judul lowongan").fill("Senior Product Engineer");
  await page.getByRole("button", { name: "Simpan perubahan" }).click();

  await expect(page).toHaveURL(/\/dashboard$/);
  await expect(
    page.getByRole("article", { name: "Senior Product Engineer" }),
  ).toBeVisible();
  await expect(
    page.getByRole("article", { name: "Product Engineer", exact: true }),
  ).toBeHidden();
});

test("recruiter can delete a vacancy", async ({ page }) => {
  await page.goto("/dashboard");

  const productCard = page.getByRole("article", {
    name: "Product Engineer",
  });
  page.once("dialog", (dialog) => dialog.accept());
  await productCard.getByRole("button", { name: "Hapus" }).click();

  await expect(productCard).toBeHidden();
  await expect(
    page.getByRole("article", { name: "Android Developer" }),
  ).toBeVisible();
});

async function fillVacancyForm(page: Page, title: string) {
  await page.getByLabel("Judul lowongan").fill(title);
  await page.getByLabel("Posisi").selectOption("Software Engineering");
  await page.getByLabel("Full-Time").check();
  await page.getByLabel("Kandidat yang dibutuhkan").fill("2");
  await page.getByLabel("Aktif hingga").fill("2026-12-30");
  await page.getByLabel("Lokasi").selectOption("Bandung");
  await expect(
    page.getByRole("textbox", { name: "Deskripsi" }),
  ).not.toBeEmpty();
  await page.getByRole("spinbutton", { name: "Gaji minimum" }).fill("8000000");
  await page.getByLabel("1-3 tahun").check();
}

async function mockVacancyApi(page: Page) {
  let storedVacancies = structuredClone(initialVacancies);

  await page.route("**/api/v1/vacancies**", async (route) => {
    const request = route.request();
    const requestUrl = new URL(request.url());
    const detailMatch = requestUrl.pathname.match(/\/vacancies\/(\d+)$/);
    const vacancyId = detailMatch ? Number(detailMatch[1]) : null;

    if (request.method() === "POST" && vacancyId === null) {
      const payload = request.postDataJSON();
      const savedVacancy = vacancyFromPayload(
        payload,
        Math.max(...storedVacancies.map(({ id }) => id), 0) + 1,
      );
      storedVacancies = [savedVacancy, ...storedVacancies];

      await fulfillJson(route, 201, {
        message: "Vacancy created successfully.",
        data: savedVacancy,
      });
      return;
    }

    if (request.method() === "PUT" && vacancyId !== null) {
      const index = storedVacancies.findIndex(({ id }) => id === vacancyId);

      if (index < 0) {
        await fulfillJson(route, 404, { message: "Vacancy not found." });
        return;
      }

      const payload = request.postDataJSON();
      storedVacancies[index] = vacancyFromPayload(payload, vacancyId, {
        createdAt: storedVacancies[index].created_at,
      });

      await fulfillJson(route, 200, {
        message: "Vacancy updated successfully.",
        data: storedVacancies[index],
      });
      return;
    }

    if (request.method() === "DELETE" && vacancyId !== null) {
      storedVacancies = storedVacancies.filter(({ id }) => id !== vacancyId);
      await route.fulfill({
        status: 204,
        headers: corsHeaders,
      });
      return;
    }

    if (request.method() === "GET" && vacancyId !== null) {
      const vacancy = storedVacancies.find(({ id }) => id === vacancyId);

      if (!vacancy) {
        await fulfillJson(route, 404, { message: "Vacancy not found." });
        return;
      }

      await fulfillJson(route, 200, { data: vacancy });
      return;
    }

    const search = requestUrl.searchParams.get("search")?.toLowerCase() ?? "";
    const filteredVacancies = storedVacancies.filter((vacancy) =>
      vacancy.title.toLowerCase().includes(search),
    );

    await fulfillJson(route, 200, {
      data: filteredVacancies,
      links: {
        first: requestUrl.toString(),
        last: requestUrl.toString(),
        prev: null,
        next: null,
      },
      meta: {
        current_page: 1,
        from: filteredVacancies.length > 0 ? 1 : null,
        last_page: 1,
        per_page: 20,
        to: filteredVacancies.length || null,
        total: filteredVacancies.length,
      },
    });
  });
}

function vacancyFromPayload(
  payload: Record<string, unknown>,
  id: number,
  options: { createdAt?: string | null } = {},
) {
  const employmentType = String(payload.employment_type);
  const minimumExperience = String(payload.minimum_experience);

  return {
    id,
    title: String(payload.title),
    position: String(payload.position),
    employment_type: employmentType,
    employment_type_label:
      employmentLabels[employmentType] ?? employmentType,
    location: String(payload.location),
    is_remote: Boolean(payload.is_remote),
    minimum_experience: minimumExperience,
    minimum_experience_label:
      experienceLabels[minimumExperience] ?? minimumExperience,
    expires_at: String(payload.expires_at),
    is_active: true,
    created_at: options.createdAt ?? "2026-07-29T08:00:00.000000Z",
    candidate_count: Number(payload.candidate_count),
    description: String(payload.description),
    salary_min: Number(payload.salary_min),
    salary_max:
      payload.salary_max === undefined ? null : Number(payload.salary_max),
    show_salary: Boolean(payload.show_salary),
    updated_at: "2026-07-29T08:00:00.000000Z",
    company: companyDetail,
  };
}

function createVacancyRecord({
  id,
  title,
  position,
  minimumExperience,
  minimumExperienceLabel,
  isRemote = false,
}: {
  id: number;
  title: string;
  position: string;
  minimumExperience: string;
  minimumExperienceLabel: string;
  isRemote?: boolean;
}) {
  return {
    id,
    title,
    position,
    employment_type: "full-time",
    employment_type_label: "Full-Time",
    location: "Bandung",
    is_remote: isRemote,
    minimum_experience: minimumExperience,
    minimum_experience_label: minimumExperienceLabel,
    expires_at: "2026-12-30",
    is_active: true,
    created_at: "2026-07-27T08:00:00.000000Z",
    candidate_count: 1,
    description: [
      "<h2>Job Description</h2>",
      "<p>Build impactful products for Dicoding users.</p>",
      "<h2>Responsibilities</h2>",
      "<ul><li>Collaborate with designers and engineers.</li></ul>",
    ].join(""),
    salary_min: 8_000_000,
    salary_max: 12_000_000,
    show_salary: true,
    updated_at: "2026-07-28T08:00:00.000000Z",
    company: companyDetail,
  };
}

async function fulfillJson(
  route: Parameters<Parameters<Page["route"]>[1]>[0],
  status: number,
  body: unknown,
) {
  await route.fulfill({
    status,
    contentType: "application/json",
    headers: corsHeaders,
    body: JSON.stringify(body),
  });
}

const employmentLabels: Record<string, string> = {
  "full-time": "Full-Time",
  "part-time": "Part-Time",
  contract: "Kontrak",
  internship: "Intern",
};

const experienceLabels: Record<string, string> = {
  "less-than-1-year": "Kurang dari 1 tahun",
  "1-3-years": "1-3 tahun",
  "4-5-years": "4-5 tahun",
  "6-10-years": "6-10 tahun",
  "more-than-10-years": "Lebih dari 10 tahun",
};

const corsHeaders = {
  "access-control-allow-origin": "http://127.0.0.1:3000",
  "access-control-allow-methods": "GET, POST, PUT, DELETE, OPTIONS",
  "access-control-allow-headers": "Accept, Content-Type",
};
