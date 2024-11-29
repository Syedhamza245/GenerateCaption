"use client";
import { useRouter } from "next/navigation";

const Logout = () => {
  const router = useRouter();
  const logout = async () => {
    await (
      await fetch("/api/logout", {
        method: "POST",
        credentials: "include",
      })
    ).json();
    router.refresh();
  };
  return (
    <button onClick={logout} className="bg-red-500 px-4 py-2 rounded-xl">
      Logout
    </button>
  );
};

export default Logout;
