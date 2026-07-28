function getApiBaseUrl(): string {
  const apiBaseUrl = process.env.NEXT_PUBLIC_API_BASE_URL;

  if (!apiBaseUrl) {
    throw new Error(
      "NEXT_PUBLIC_API_BASE_URL is not configured. " +
        "Create frontend/.env.local based on frontend/.env.example.",
    );
  }

  return apiBaseUrl.replace(/\/+$/, "");
}

export const env = Object.freeze({
  apiBaseUrl: getApiBaseUrl(),
});
