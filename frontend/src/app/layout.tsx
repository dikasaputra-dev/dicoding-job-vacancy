import type { Metadata } from "next";
import { ReactNode } from "react";
import { QueryProvider } from "@/providers/query-provider";

import "./globals.css";

export const metadata: Metadata = {
  title: "Dicoding Jobs",
  description: "Discover career opportunities at Dicoding Indonesia.",
};

interface RootLayoutProps {
  children: ReactNode;
}

export default function RootLayout({ children }: RootLayoutProps) {
  return (
    <html lang="id">
      <body>
        <QueryProvider>{children}</QueryProvider>{" "}
      </body>
    </html>
  );
}
