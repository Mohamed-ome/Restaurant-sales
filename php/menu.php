<?php include 'includes/header.php'; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="glass-card p-4">
            <h6 class="text-white fw-bold mb-4 italic">إدارة الأقسام</h6>
            <div class="space-y-2">
                <div class="d-flex justify-content-between align-items-center p-3 bg-zinc-950 rounded-3 border border-amber-500/20">
                    <span class="text-xs text-white fw-bold">المأكولات</span>
                    <i data-lucide="chevron-left" class="text-amber-500" style="width: 14px;"></i>
                </div>
                <div class="d-flex justify-content-between align-items-center p-3 bg-zinc-950/20 rounded-3 border border-zinc-800 hover:border-zinc-700 transition-all cursor-pointer">
                    <span class="text-xs text-zinc-400">العصائر</span>
                    <i data-lucide="chevron-left" class="text-zinc-600" style="width: 14px;"></i>
                </div>
            </div>
            <button class="btn btn-outline-secondary w-full mt-4 border-dashed border-zinc-800 text-zinc-500 text-[10px] fw-bold py-2">
                إضافة قسم جديد +
            </button>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="text-white fw-bold mb-0 italic">الأصناف (المأكولات)</h6>
                <button class="btn btn-accent text-[10px] fw-bold px-4">إضافة صنف +</button>
            </div>

            <div class="row row-cols-1 row-cols-md-2 g-3">
                <?php 
                $menu = [
                    ['name' => 'بيتزا مارجريتا', 'price' => 120, 'desc' => 'عجينة رقيقة، طماطم، موزاريلا، ريحان'],
                    ['name' => 'برجر دجاج كرسبي', 'price' => 85, 'desc' => 'صدر دجاج مقلي، خس، مايونيز، بطاطس'],
                    ['name' => 'شاورما لحم', 'price' => 95, 'desc' => 'لحم بلدي، طحينة، بقدونس، مخلل'],
                    ['name' => 'باستا ألفريدو', 'price' => 110, 'desc' => 'مكرونة، كريمة، دجاج، فطر'],
                ];
                foreach($menu as $item): ?>
                <div class="col">
                    <div class="p-3 bg-zinc-950/30 rounded-4 border border-zinc-900 d-flex gap-3 align-items-center hover:bg-zinc-950/50 transition-all">
                        <div class="bg-zinc-900 rounded-3 text-center d-flex align-items-center justify-content-center border border-zinc-800" style="width: 60px; height: 60px; flex-shrink: 0;">
                            <span class="text-zinc-700 fw-bold fs-4">P</span>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex justify-content-between">
                                <h6 class="text-zinc-200 fw-bold mb-1 text-xs text-truncate"><?php echo $item['name']; ?></h6>
                                <span class="text-amber-500 fw-bold text-xs"><?php echo $item['price']; ?> ر.س</span>
                            </div>
                            <p class="text-zinc-600 mb-0 italic text-truncate" style="font-size: 9px;"><?php echo $item['desc']; ?></p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-link text-zinc-700 p-0 shadow-none" data-bs-toggle="dropdown">
                                <i data-lucide="more-vertical" style="width: 14px;"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-dark bg-zinc-900 border-zinc-800 shadow-xl">
                                <li><a class="dropdown-item text-xs" href="#">تعديل</a></li>
                                <li><a class="dropdown-item text-xs text-danger" href="#">حذف</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
