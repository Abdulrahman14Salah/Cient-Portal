<x-guest-layout>

    <div class="max-w-3xl mx-auto py-10">

        <h1 class="text-2xl font-bold mb-6">ابدأ طلبك</h1>

        <form method="POST" action="{{ route('apply.store') }}" class="space-y-6">
            @csrf

            {{-- Name --}}
            <input name="name" placeholder="الاسم" class="w-full border p-3 rounded" required>

            {{-- Email --}}
            <input name="email" type="email" placeholder="الإيميل" class="w-full border p-3 rounded" required>

            {{-- Phone --}}
            <input name="phone" placeholder="رقم الهاتف" class="w-full border p-3 rounded">

            {{-- Adults --}}
            <input name="adults" type="number" placeholder="عدد البالغين" class="w-full border p-3 rounded" required>

            {{-- Kids --}}
            <input name="kids" type="number" placeholder="عدد الأطفال" class="w-full border p-3 rounded">

            {{-- Nationality --}}
            <input name="nationality" placeholder="الجنسية" class="w-full border p-3 rounded" required>

            {{-- Country --}}
            <input name="country" placeholder="الدولة" class="w-full border p-3 rounded" required>

            {{-- City --}}
            <input name="city" placeholder="المدينة" class="w-full border p-3 rounded" required>

            {{-- Employment --}}
            <select name="employment" class="w-full border p-3 rounded">
                <option value="employee">موظف</option>
                <option value="self_employed">عمل حر</option>
            </select>

            {{-- Remote --}}
            <select name="remote" class="w-full border p-3 rounded">
                <option value="1">Remote</option>
                <option value="0">Office</option>
            </select>

            {{-- Income --}}
            <input name="income" type="number" placeholder="الدخل" class="w-full border p-3 rounded" required>

            {{-- Move Date --}}
            <input name="move_date" type="date" class="w-full border p-3 rounded">

            {{-- Notes --}}
            <textarea name="notes" placeholder="ملاحظات" class="w-full border p-3 rounded"></textarea>

            <button class="bg-indigo-600 text-white px-6 py-3 rounded w-full">
                إرسال الطلب
            </button>

        </form>

    </div>

</x-guest-layout>
