import { NextResponse } from "next/server";
import type { NextRequest } from "next/server";

export function middleware(request: NextRequest) {
  // Get the `auth_token` cookie
  const authToken = request.cookies.get("auth_token")?.value;
  const pathname = request.nextUrl.pathname;
  const publicRoute = ["/signup", "login"];
  const privateRoute = ["/generatecaption", "/home", "/pricing"];
  const isPublicROute = publicRoute.includes(pathname);
  const isPrivateRoute = privateRoute.includes(pathname);

  if (isPrivateRoute && !authToken) {
    return NextResponse.redirect(new URL("/login", request.url));
  }

  if (isPublicROute && authToken) {
    return NextResponse.redirect(new URL("/pricing", request.url));
  }
  return NextResponse.next();
}

export const config = {
  matcher: ["/((?!api|_next/static|_next/image|.*\\.png$).*)"],
};







