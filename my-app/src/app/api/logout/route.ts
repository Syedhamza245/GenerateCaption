import { cookies } from "next/headers";
import { NextResponse } from "next/server";

export const POST = (req: Request) => {
  try {
    cookies().delete("auth_token");
    return NextResponse.json({ ok: 1 }, { status: 200 });
  } catch (error) {
    return NextResponse.json({ message: "server error" }, { status: 500 });
  }
};
