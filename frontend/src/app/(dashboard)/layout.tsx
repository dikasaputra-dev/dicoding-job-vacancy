import type { ReactNode } from "react";

import { SiteHeader } from "@/components/layout/site-header";

interface DashboardLayoutProps {
  children: ReactNode;
}

export default function DashboardLayout({
  children,
}: DashboardLayoutProps) {
  return (
    <div className="min-h-screen bg-white">
      <SiteHeader />
      {children}
    </div>
  );
}
