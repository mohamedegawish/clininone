@extends('layouts.app')

@section('title', 'إدارة المواعيد')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">إجمالي المواعيد في النظام</h1>
        <p class="page-subtitle">متابعة كافة المواعيد عبر جميع العيادات</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">قائمة المواعيد الكلية</h3>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>العيادة</th>
                    <th>المريض</th>
                    <th>الطبيب</th>
                    <th>التاريخ</th>
                    <th>الحالة</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">لا توجد بيانات مواعيد حالياً.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
