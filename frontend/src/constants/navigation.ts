export interface NavigationItem {
  href: string;
  label: string;
  activePaths: string[];
}

export const NAVIGATION_ITEMS: NavigationItem[] = [
  {
    href: "/",
    label: "Lowongan Kerja",
    activePaths: ["/"],
  },
  {
    href: "/dashboard",
    label: "Dashboard",
    activePaths: ["/dashboard"],
  },
];
